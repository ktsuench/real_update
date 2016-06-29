'use strict';

/**
 * Some constant values.
 * Put here to make it easier to manage/change if required.
 * @type {String}
 */
var container_classname = 'scroll-content-parent';
var wrapper_classname = 'scroll-content';
var content_classname = 'flex-panel';
var content_element_classnames = ['flex flex-col flex-center', 'content', ['content-title', 'content-material', 'content-img']];

/**
 * Collection of timers set to control scroll speed.
 * Needs to be cleared everytime display content is updated from the db.
 * @type {Array}
 */
var scroll_timers = [];

/**
 * Setup and starts the loop for scrolling the contents in the scrolling containers.
 * @param  {String} frame   Id of the parent container of the scrolling containers.
 * @return {Null}
 */
function scroll_start(frame){
    /**
     * Get the parent of the scrolling container.
     * @type {HTMLCollection}
     */
    var parent_frame = document.getElementById(frame);

    //Make it so that all children of the frame will be the same width.
    (Array.from(parent_frame.children)).forEach( function(child, index) {
        child.setAttribute('style', 'width:' + (parent_frame.scrollWidth / parent_frame.children.length - 5) + 'px');
    });

    /**
     * Retrieve HTMLCollection object of elements with given classname and then put them
     * put them into an array. This will remove duplicates of elements (HTMLCollection
     * lists elements with numerical index and then with element id index, thus there
     * will be duplicates if not handled properly).
     * @type {Array}
     */
    var containers = Array.from(document.getElementsByClassName(container_classname));

    /**
     * Scrolling type as set in system settings.
     * @type {Boolean}
     */
    //TODO: bring this over to the php page so that the saved setting can be assigned.
    var discontinuous = true;

    /**
     * Scrolling refresh rate if discontinuous.
     * @type {Number}
     */
    //TODO: bring this over to the php page so that the saved setting can be assigned.
    var speed = 1000 * 5;

    if(containers.length > 0){
        containers.forEach( function(container, index) {
            var wrappers = Array.from(container.children);
            var i = 0;

            wrappers.forEach( function(wrapper, index) {
                if(wrapper.classList.contains(wrapper_classname)){
                    //Need to get container width so that all content panels are the size of the container.
                    var container_width = container.getAttribute('style');
                    var style_property = 'width:';
                    var style_unit = 'px';
                    var search_start = container_width.indexOf(style_property) + style_property.length;
                    var search_end = container_width.indexOf(style_unit) - search_start;

                    container_width = container_width.substr(search_start, search_end);

                    //Set the content panel widths of the wrapper to the width of the container.
                    var contents = Array.from(wrapper.children);
                    var i = 0;

                    if(contents.length > 0){
                        contents.forEach( function(content, index) {
                            if(content.classList.contains(content_classname)){
                                content.setAttribute('style', 'width:' + (container_width - 1) + 'px;');


                                var el = Array.from(content.querySelector('.'+content_element_classnames[1]).children);
                                var content_height = 0;
                                var img;

                                //Calculate the total heigh of the content
                                el.forEach( function(e, index) {
                                    if(e.tagName.search(/img/i) > -1) img = e;
                                    content_height += parseInt(e.clientHeight);
                                });

                                //Check that the last element in the content container is an image tag
                                if(img !== undefined && img.tagName.search(/img/i) > -1){
                                    //Decrease the width of the image if it is greater than the width of the container
                                    if(parseInt(img.width) > parseInt(content.style.width) && 
                                        parseInt(img.width) > parseInt(img.height)){
                                        img.classList.add(content_element_classnames[2][2] + '-width');
                                    //Decrease the height of the image if its height combined with the
                                    //rest of the content is greater than the height of the container
                                    }else if(content_height > parseInt(content.clientHeight)){
                                        img.classList.add(content_element_classnames[2][2] + '-height');
                                    }
                                }
                                i++;
                            }
                        });

                        var wrapper_width = (container_width - 1) * i;
                        var wrapper_position = - (wrapper_width - (container_width - 1));
                        wrapper.setAttribute('style', 'width:' + wrapper_width + 'px; left:' + wrapper_position + 'px;');

                        //Only start the scroll timers if there is more than two pieces of content to show
                        if(contents.length > 2){
                            var panel_num = 2;

                            //Type of content scrolling.
                            //Note: 1000ms = 1s
                            scroll_timers.push(window.setInterval(function(){
                                if(discontinuous){
                                    if(wrapper_width < (container_width - 1) * panel_num) panel_num = 1;
                                    var wrapper_position = - (wrapper_width - (container_width - 1) * panel_num++);
                                }else{
                                    if(wrapper_width < panel_num + (container_width - 1) - 75) panel_num = 1;
                                    var wrapper_position = - (wrapper_width - (container_width - 1) - panel_num);
                                    panel_num += 5;
                                }

                                wrapper.setAttribute('style', 'width:' + wrapper_width + 'px; left:' + wrapper_position + 'px;');
                            }, discontinuous === true ? speed : 70));
                        }

                        console.log('Updated timers @ ' + (new Date()));
                    }else{
                        console.log('Failed to load content for ' + container.getAttribute('id') + '.');
                    }
                }
            });
        });
    }
}

/**
 * Setup and start the loop for checking for updates to the display.
 * @param  {String} class_name  Display content container classname.
 * @return {Null}
 */
function update_start(class_name){
    var hooks = '';

    if((hooks = Array.from(document.getElementsByClassName(class_name))) != null){
        var data = '';

        var xhr = new XMLHttpRequest();
        xhr.open('post', update_display);
        
        xhr.onreadystatechange = function(){
            var DONE = 4; //request is complete
            var OK = 200; //successful operation
            
            if(xhr.readyState === DONE){
                if(xhr.status === OK){
                    data = JSON.parse(xhr.responseText);

                    //Check that there is content to be displayed
                    if(Object.keys(data).length > 0){
                        data = Object.keys(data).map(function(key){return data[key];});
                        data = data.reverse();
                    }

                    hooks.forEach( function(hook, index) {
                        //Set up the template of the panels
                        var title = document.createElement('DIV');
                        var content = document.createElement('DIV');
                        var image = document.createElement('IMG');
                        var container = {
                            'top': document.createElement('DIV'),
                            'mid': document.createElement('DIV'),
                            'bot': document.createElement('DIV') 
                        };

                        title.classList.add(content_element_classnames[2][0]);
                        content.classList.add(content_element_classnames[2][1]);
                        container.top.classList.add(content_classname);

                        //Add class names to the containers
                        ['mid', 'bot'].forEach( function(box, index) {
                            if(content_element_classnames[index].split(' ').length > 1){
                                content_element_classnames[index].split(' ').forEach( function(cls, index) {
                                    container[box].classList.add(cls);
                                });
                            }else container[box].classList.add(content_element_classnames[index]);
                        });

                        hook.innerHTML = '';

                        //Check that there is content to be displayed
                        if(Object.keys(data).length > 0){
                            //Replace the current content
                            for(var item in data){
                                title.innerHTML = data[item].title;
                                image.src = data[item].image !== null ? upload_path + data[item].image : '';
                                content.innerHTML = data[item].content;

                                var image_exists = window.location.href && image.src.length;

                                container.bot.innerHTML = title.outerHTML + content.outerHTML;
                                container.bot.innerHTML += image.src != !image_exists > 0 ? image.outerHTML : '';
                                container.mid.innerHTML = container.bot.outerHTML;
                                container.top.innerHTML = container.mid.outerHTML;

                                hook.innerHTML += container.top.outerHTML;
                            }
                        }else{
                            //TODO: Allow system admin to set default message
                            title.innerHTML = 'No Announcements on Display';
                            content.innerHTML = 'There\'s nothing to show';

                            container.bot.innerHTML = title.outerHTML + content.outerHTML;
                            container.mid.innerHTML = container.bot.outerHTML;
                            container.top.innerHTML = container.mid.outerHTML;

                            hook.innerHTML += container.top.outerHTML;
                        }

                        //Check that there is content to be displayed
                        if(Object.keys(data).length > 0){
                            //Set up data order for the next panel
                            data.push(data.shift());
                        }
                    });

                    //Reset all the scroll timers that are running
                    reset_scroll_timers();

                    console.log('Updated content @ ' + (new Date()));
                }else console.log('Error updating display. Server Error: ' + xhr.status);
            }
        }
        
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send();
    }else{
        console.log('Nothing to update.');
    }
}

/**
 * Clear the existing timers for scrolling the content and create new ones.
 * In other words, restart the whole process.
 * 
 * @return {Null}
 */
function reset_scroll_timers(){
    scroll_timers.forEach( function(timer, index) {
        window.clearInterval(timer);
    });

    scroll_timers = [];
    scroll_start('display');
}

/**
 * [refresh_weather description]
 * @param  {String} id Weather Element ID
 * @return {Null}
 */
function refresh_weather(id){
    var data;

    var xhr = new XMLHttpRequest();
    xhr.open('post', update_weather);

    xhr.onreadystatechange = function(){
        var DONE = 4; //request is complete
        var OK = 200; //successful operation
        
        if(xhr.readyState === DONE){
            if(xhr.status === OK){
                var weather_container = document.getElementById(id);
                var weather_icon;
                var weather_temp;
                var weather_desc;

                data = JSON.parse(xhr.responseText);

                weather_icon = '<i class="wi wi-owm-' + data.weather[0].id + '"></i>';
                weather_temp = '<span style="margin-left:0.3em;">' + data.main.temp + '<i class="wi wi-celsius"></i></span>';
                weather_desc = '<span style="text-transform:capitalize;">' + data.weather[0].description + '</span>';
                weather_container.innerHTML = '<div>' + weather_icon + weather_temp + '</div>' + weather_desc;

                console.log('Updated weather @ ' + (new Date()))
            }else console.log('Error updating weather. Server Error: ' + xhr.status);
        }
    }

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send();
}

//Datetime constants
var month_names = [ 'Jan', 'Feb', 'March', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec' ];
var day_names = ['Sun','Mon','Tue','Wed','Thur','Fri','Sat'];

/**
 * Updates the date on the display
 * @param  {String} id Date Element ID
 * @return {Null}
 */
function retrieve_date(id){
    // Create a newDate() object
    var nd = new Date();
    // Extract the current date from Date object
    nd.setDate(nd.getDate());
    // Output the day, date, month and year
    var clock_date = document.getElementById(id);
    clock_date.innerHTML = day_names[nd.getDay()] + ", " + nd.getDate();
    clock_date.innerHTML += " " + month_names[nd.getMonth()] + ", " + nd.getFullYear();
}

/**
 * Updates the time on the display
 * @param  {String} h   Hour Element ID
 * @param  {String} m   Minute Element ID
 * @param  {String} s   Second Element ID
 * @param  {String} mer Meridian Element ID
 * @return {Null}
 */
function retrieve_time(h,m,s,mer){
    var clock_hour = document.getElementById(h);
    var clock_min = document.getElementById(m);
    var clock_sec = document.getElementById(s);
    var clock_meridian = document.getElementById(mer);

    var ref_date = new Date();
    var seconds = ref_date.getSeconds();
    var minutes = ref_date.getMinutes();
    var hours = ref_date.getHours();

    clock_sec.innerHTML = ( seconds < 10 ? "0" : "" ) + seconds;
    clock_min.innerHTML = ( minutes < 10 ? "0" : "" ) + minutes;
    clock_hour.innerHTML = ((hours > 12 ? hours - 12 : hours) < 10 ? "0" : "" ) + (hours > 12 ? hours - 12 : hours);
    clock_meridian.innerHTML = (hours > 12 ? "PM" : "AM");

    if(hours===24){retrieve_date('date');}
}

//setInterval reference object
var resize_event;

/**
 * Initialize the various components of the display
 * @return {Null}
 */
function initialize_display(){
    //Start the datetime of the display
    retrieve_date('date');
    retrieve_time('hour','minute','second','meridian');
    window.setInterval(function(){retrieve_time('hour','minute','second','meridian')}, 1000);

    //Start the weather update process of the display
    refresh_weather('weather');
    window.setTimeout(function(){
        window.setInterval(function(){refresh_weather('weather')}, 1000 * 60 * 10);
    }, 1000 * 60 * 30);

    //Start the scrolling content containers
    scroll_start('display');

    //Start the update content background process
    //TODO: If failed to update display due to internet connection, check again when internet is available
    var ref_time = new Date();
    window.setTimeout(function(){
        window.setInterval(function(){
            update_start('scroll-content');
        }, 1000 * 60 * 15);
    }, (15 - ref_time.getMinutes() % 15) * 60 * 1000 + ref_time.getSeconds() * 1000 + ref_time.getMilliseconds());

    //Refresh Display on Window Resize
    window.addEventListener('resize', function(e){
        window.clearTimeout(resize_event);
        if(window.innerHeight >= 620 && window.innerWidth >= 1024){
            resize_event = window.setTimeout(function(){reset_scroll_timers();}, 50);
        }
    }); 
}

initialize_display();