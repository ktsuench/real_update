'use strict';
var selected_class = 'selected';
var _selected_date = {};
var id_tracker = {};

//JavaScript for changing Calendar Year & Month
function jump_to_date(year_id, month_id, calendar_id, date_id, selected_id_prefix, y, m){
    var xhr = new XMLHttpRequest();
    var data = 'echo=TRUE&calendar_id=' + calendar_id;
    y = y === undefined ? (document.getElementById(year_id)).selectedOptions[0].value : y;
    m = m === undefined ? (document.getElementById(month_id)).selectedOptions[0].value : m;
    xhr.open('post', get_cal_url + '/' + y + '/' + m);
    
    xhr.onreadystatechange = function(){
        var DONE = 4; //request is complete
        var OK = 200; //successful operation
        var date_field = document.getElementById(date_id);
        
        if(xhr.readyState === DONE){
            if(xhr.status === OK){
                (document.getElementById(calendar_id)).innerHTML = xhr.responseText;
                assign_date_id(calendar_id, selected_id_prefix);
                assign_data_parent(calendar_id);    
                attach_date_event(date_id, selected_id_prefix, calendar_id);

                var now = new Date();
                
                if(parseInt(y) != parseInt(now.getFullYear()) || parseInt(m) != parseInt(now.getMonth() + 1)){
                    date_field.value = '1';
                    (document.getElementById(selected_id_prefix + '1')).classList.add(selected_class);
                }else{
                    date_field.value = now.getDate();
                }
                
                _selected_date[calendar_id] = document.getElementById(selected_id_prefix + date_field.value);

                (document.getElementById(year_id)).value = y;
                (document.getElementById(month_id)).value = (parseInt(m) < 10 ? '0' : '') + parseInt(m);
            }else alert('Error retrieving requested calendar. Server Error: ' + xhr.status);
        }
    }
    
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(data);
}

function jump_to_date_btn(y, m){
    if(parseInt(y) > (new Date()).getFullYear() - 1 && parseInt(y) < (new Date()).getFullYear() + 11 && parseInt(m) > 0 && parseInt(m) < 13){
        var calendar_id = event.target.getAttribute('data-parent');
        var arr = id_tracker[calendar_id];
        jump_to_date(arr[0], arr[1], calendar_id, arr[2], arr[3], y, m);
    }
}

function assign_date_id(calendar_id, selected_id_prefix){
    (Array.from(document.getElementById(calendar_id).getElementsByClassName('cal-day'))).forEach(function(el, index){
        el.setAttribute('id', selected_id_prefix + el.innerHTML);
    });
}

function assign_data_parent(calendar_id){
    (Array.from(document.getElementById(calendar_id).getElementsByClassName('month-url'))).forEach(function(el, index){
        el.setAttribute('data-parent', calendar_id);
    });
}

//Attach events
function attach_date_dropdown_events(year_id, month_id, calendar_id, date_id, selected_id_prefix){
    [month_id, year_id].forEach(function(id, index){
        var el = document.getElementById(id);
        el.onchange = function(y_id, m_id, c_id, d_id, s_id_prefix){
            jump_to_date(y_id, m_id, c_id, d_id, s_id_prefix);
        }.bind(el, year_id, month_id, calendar_id, date_id, selected_id_prefix)
    });
}

//JavaScript for filling in hidden form fields
function attach_date_event(date_id, selected_id_prefix, calendar_id){
    (Array.from(document.getElementById(calendar_id).getElementsByClassName('cal-day'))).forEach(function(el, index){
        el.onclick = function(d_id, s_id_prefix, c_id){
            var date_field = document.getElementById(d_id);
            var prev_date_selected = _selected_date[c_id];

            prev_date_selected.classList.remove(selected_class);
            
            date_field.value = this.innerHTML;
            this.classList.add(selected_class);
            _selected_date[c_id] = document.getElementById(s_id_prefix + el.innerHTML);
        }.bind(el, date_id, selected_id_prefix, calendar_id);
    });
}

function initiate_calendar(year_id, month_id, calendar_id, date_id, selected_id_prefix){
    assign_date_id(calendar_id, selected_id_prefix);
    assign_data_parent(calendar_id);

    //Select date based on hidden input field value
    var date_field = document.getElementById(date_id);
    var date_selected = document.getElementById(selected_id_prefix + date_field.value);

    date_selected.classList.add(selected_class);

    //Remove the default selected date (today) to allow the user set date to be the only one selected
    var y = parseInt((document.getElementById(year_id)).value);
    var m = parseInt((document.getElementById(month_id)).value);
    var now = new Date();

    //Check that the user set date is not the default selected date before removing selected class
    if(y == parseInt(now.getFullYear()) && m == parseInt(now.getMonth() + 1) && date_field.value != now.getDate()){
        (document.getElementById(selected_id_prefix + now.getDate())).classList.remove(selected_class);
    }

    //Used to prevent failure of js script if DOM is modified by user
    //Note: will fail if the script is altered
    _selected_date[calendar_id] = date_selected;

    attach_date_dropdown_events(year_id, month_id, calendar_id, date_id, selected_id_prefix);
    attach_date_event(date_id, selected_id_prefix, calendar_id);

    id_tracker[calendar_id] = [year_id, month_id, date_id, selected_id_prefix];
}