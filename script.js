const qs = (selector) => document.querySelector(selector);
const qsa = (selector) => document.querySelectorAll(selector);

const closeModalButtons = qsa('[data-close-button]');
const overlay = qs('#overlay');
var currentId = "-1";


refreshOpenModalButtons();

overlay.addEventListener('click', () => { qsa('.modal.active').forEach(closeModal) });

function updateDayChoice(day) {
    fetch(`get_formatted_date.php?day=${day}`)
        .then((response) => response.text())
        .then((formattedDate) => { qs('#day_choice').innerHTML = formattedDate; })
        .catch((error) => { console.error('Error fetching formatted date:', error); });
}

function refreshOpenModalButtons() {

    const assoc = (day, start_hour, start_min, end_hour, end_min, type_select, group_select, salle, matiere, enseignant) => {
        qs('#day_choice').innerHTML = day;
        const associativeArray = {
            'start_hour': start_hour,
            'start_min': start_min,
            'end_hour': end_hour,
            'end_min': end_min,
            'type_select': type_select,
            'group_select': group_select,
            'salle': salle,
            'matiere': matiere,
            'enseignant': enseignant,
        }; for (const key in associativeArray) qs(`#${key}`).value = associativeArray[key];

        ['start_hour', 'end_hour', 'type_select'].forEach(function(item) { qs(`#${item}`).dispatchEvent(new Event('change')); });
    }

    qsa('[data-modal-target]').forEach(button => {
        button.addEventListener('click', () => {
            if (isNaN(button.className)) {

                currentId = "-1";
                const [start_hour, start_min, day, group] = button.className.split('_');

                assoc(day, start_hour, start_min, start_hour, start_min, "Cours", group, "", "", "")

            } else {

                fetch('week.json?_=' + new Date().getTime())
                    .then((response) => response.json())
                    .then((data) => {
                        for (var x in data[2]) {
                            var l = data[2][x].length;
                            for (var y = 0; y < l; y++) {
                                var item = data[2][x][y];
                                if (item != null && item.id == button.className) {

                                    currentId = item.id;
                                    const [start_hour, start_min] = x.split('h');
                                    const end_hour = parseInt(start_hour) + Math.floor(parseInt(item.duree) / 4);
                                    const end_min = parseInt(start_min) + ((parseInt(item.duree) % 4) * 15);

                                    assoc(item.date, start_hour, start_min, end_hour + (end_min >= 60 ? 1 : 0), (end_min % 60 === 0) ? '00' : end_min % 60, item.type, item.groupe, item.salle, item.matiere, item.enseignant)

                                    return;
                                }
                            }
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching data:', error);
                    });
            } openModal(qs(button.dataset.modalTarget))
        })
    })
}


function openModal(modal) {
    if (!modal) return
    modal.classList.add('active')
    overlay.classList.add('active')
}

closeModalButtons.forEach(button => {
    button.addEventListener('click', () => {
        closeModal(button.closest('.modal'))
    })
})

function closeModal(modal) {
    if (!modal) return
    modal.classList.remove('active')
    overlay.classList.remove('active')
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Avoid forbidden hours like 8:00 and 19:15

['end_hour', 'start_hour'].forEach((item) => { qs(`#${item}`).onchange = (event) => avoidForbidden(event); })

function avoidForbidden(event) {
    const [select, option1, option2, option3] = ["", "00", "30", "45"].map(item => document.getElementById(`${event.target.id.split('_')[0]}_min${item}`));
    option1.disabled = event.target.value == '8';
    [option2, option3].forEach(item => item.disabled = event.target.value == '19');
    select.value = (event.target.value == '8' && select.value == '00') ? '15' : (event.target.value == '19') ? '00' : select.value;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

qs('#type_select').onchange = (event) => disabledGroup(event);

function disabledGroup(event) {
    const groupEl = qs('#group_select');
    const isDisabled = event.target.value === 'Cours';

    groupEl.disabled = (isDisabled);
    if (!isDisabled && groupEl.value === '') groupEl.value = 'G1';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

qs('#save-button').onclick = () => onSaveButtonClick();

function onSaveButtonClick() {
    const values = ["start_hour", "start_min", "end_hour", "end_min", "type_select", "group_select", "matiere", "enseignant", "salle"].map((id) => $(`#${id}`).val());
    const [start_hour, start_min, end_hour, end_min, type, group, matiere, enseignant, salle] = values;

    const date = $("#day_choice").html();
    const invalidInputs = ["matiere", "enseignant", "salle"].filter((id) => !$("#" + id).val());
    
    invalidInputs.forEach((id) => $(`#${id}`).css("border", "1px solid red"));

    const [s_hour, s_min, e_hour, e_min] = [parseInt(start_hour), parseInt(start_min), parseInt(end_hour), parseInt(end_min)];

    if (invalidInputs.length || (s_hour > e_hour) || (s_hour === e_hour && s_min > e_min)) return;

    const successCallback = () => {
        updateTable();
    };

    const errorCallback = () => {};

    const postData = {
        url: "add_course_v2.php",
        type: "POST",
        data: {data: [currentId, date, ...values]},
        success: successCallback,
        error: errorCallback
    };

    if (currentId != "-1") {
        $.ajax({
            url: "delete_course.php",
            type: "POST",
            data: {id: currentId},
            success: () => { $.ajax(postData); },
            error: errorCallback
        });
    } else {
        $.ajax(postData);
    }
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

qs('#logout').onclick = () => { changeVariable('logout', ''); }
qs('#next_week').onclick = () => { changeVariable('changeWeek', 'next'); }
qs('#previous_week').onclick = () => { changeVariable('changeWeek', 'previous'); }

function changeVariable(action, value) {
    $.ajax({
        type: 'POST',
        url: 'change_variable.php',
        data: { action: action, value: value },
        dataType: 'text',
        success: function(responseText) {
            switch (responseText) {
                case 'success logout':
                    window.location.href = 'index.php';
                    break;
                case 'success changeWeek':
                    updateTable();
                    updateCurrentWeek()
                    break;
                default:
                    console.log('Error: ' + responseText);
                    break;
            }
        },
        error: function() {
            console.log('Error: Unable to complete the request.');
        }
    });
}

function updateTable() {
    $.ajax({
        type: 'GET',
        url: 'fetch_table.php',
        dataType: 'html',
        success: function(responseText) {
            $('#myTable').html(responseText);
            refreshOpenModalButtons();
        },
        error: function() {
            console.log('Error: Unable to update the table.');
        }
    });
}

function updateCurrentWeek() {
    $.ajax({
        type: 'GET',
        url: 'get_current_week.php',
        dataType: 'html',
        success: function(responseText) {
            $('#current-week').html(responseText);
        },
        error: function() {
            console.log('Error: Unable to update the table.');
        }
    });
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////















////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// Validate button

// document.getElementById('save-button').onclick = () => {          
//     console.log('coucou');
// }

// const table = document.getElementById('tableau');

// table.addEventListener('click', function (e) {
//     const cell = e.target.closest('td');
//     if (!cell) return;
//     console.log(cell.className);
// });