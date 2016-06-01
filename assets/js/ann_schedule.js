'use strict';
//JavaScript for changing Calendar Year & Month
function jump_to_date(y, m){
    var xhr = new XMLHttpRequest();
    var data = 'echo=TRUE';
    xhr.open('post', get_cal_url + '/' + y + '/' + m);
    
    xhr.onreadystatechange = function(){
        var DONE = 4; //request is complete
        var OK = 200; //successful operation
        var calendar = document.getElementById('calendar');
        var date_field = document.getElementById('date');
        
        if(xhr.readyState === DONE){
            if(xhr.status === OK){
                calendar.innerHTML = xhr.responseText;
                attach_date_event();
                
                var now = new Date();
                
                if(parseInt(y) != parseInt(now.getFullYear()) || parseInt(m) != parseInt(now.getMonth() + 1)){
                    date_field.value = '1';
                    var first_day = document.getElementById('cal_day_1');
                    first_day.className += ' selected';
                }else{
                    date_field.value = now.getDate();
                }
                
                var year_field = document.getElementById('year');
                var month_field = document.getElementById('month');
                
                year_field.value = y;
                month_field.value = (parseInt(m) < 10 ? '0' : '') + parseInt(m);
            }else alert('Error retrieving requested calendar. Server Error: ' + xhr.status);
        }
    }
    
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(data);
}

//Attach events
var month = document.getElementById('month');
var year = document.getElementById('year');

month.onchange = function(){
    var year = document.getElementById('year');
    var year_val = year.selectedOptions[0].value;
    
    jump_to_date(year_val, this.value);
}

year.onchange = function(){
    var month = document.getElementById('month');
    var month_val = month.selectedOptions[0].value;
    
    jump_to_date(this.value, month_val);
}

//JavaScript for filling in hidden form fields
function attach_date_event(){
    var date_arr = document.getElementsByClassName('cal_day');
    
    for(var date in date_arr){
        if(date_arr.hasOwnProperty(date)){
            date_arr[date].onclick = function(){
                var date_field = document.getElementById('date');
                var prev_date_selected = document.getElementById('cal_day_' + date_field.value);
                var cls = prev_date_selected.className.split(' ');
                
                cls.splice(cls.length-1);
                prev_date_selected.className = cls.join(' ');
                
                date_field.value = this.innerHTML;
                this.className += ' selected';
            }
        }
    }
}

attach_date_event();

//Select date based on hidden input field value
var date_field = document.getElementById('date');
var date_selected = document.getElementById('cal_day_' + date_field.value);

date_selected.className += ' selected';

//Remove the today selected date
var year_field = document.getElementById('year');
var month_field = document.getElementById('month');
var y = parseInt(year_field.value);
var m = parseInt(month_field.value);
var now = new Date();

if(y == parseInt(now.getFullYear()) && m == parseInt(now.getMonth() + 1)){
    var rm_date_selected = document.getElementById('cal_day_' + now.getDate());
    var cls = rm_date_selected.className.split(' ');

    cls.splice(cls.length-1);
    rm_date_selected.className = cls.join(' ');
}