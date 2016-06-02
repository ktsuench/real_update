'use strict';

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
        child.setAttribute('style', 'width:' + (parent_frame.scrollWidth / parent_frame.children.length - 2) + 'px');
    });

    /**
     * Some constant values.
     * Put here to make it easier to manage/change if required.
     * @type {String}
     */
    var container_classname = 'scroll-content-parent';
    var wrapper_classname = 'scroll-content';
    var content_classname = 'announcement';

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
                if(wrapper.getAttribute('class').indexOf(wrapper_classname) > -1){
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
                            if(content.getAttribute('class').indexOf(content_classname) > -1){
                                content.setAttribute('style', 'width:' + (container_width - 1) + 'px; height:90vh');
                                i++;
                            }
                        });

                        var wrapper_width = (container_width - 1) * i;
                        var wrapper_position = - (wrapper_width - (container_width - 1));
                        wrapper.setAttribute('style', 'width:' + wrapper_width + 'px; left:' + wrapper_position + 'px;');

                        var panel_num = 2;
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
                    data = Object.keys(data).map(function(key){return data[key];});
                    data = data.reverse();

                    hooks.forEach( function(hook, index) {
                        //Set up the template of the panels
                        var title = document.createElement('H4');
                        var content = document.createElement('DIV');
                        var container = {
                            'top': document.createElement('DIV'),
                            'mid': document.createElement('DIV'),
                            'bot': document.createElement('DIV') 
                        };

                        container.top.setAttribute('class', 'announcement');
                        container.mid.setAttribute('class', 'flex-center');
                        container.bot.setAttribute('class', 'content');

                        hook.innerHTML = '';

                        //Replace the current content
                        for(var item in data){
                            title.innerHTML = data[item].title;
                            content.innerHTML = data[item].content;

                            container.bot.innerHTML = title.outerHTML + content.outerHTML;
                            container.mid.innerHTML = container.bot.outerHTML;
                            container.top.innerHTML = container.mid.outerHTML;

                            hook.innerHTML += container.top.outerHTML;
                        }

                        //Reset all the scroll timers that are running
                        reset_scroll_timers();

                        //Set up data order for the next panel
                        data.push(data.shift());
                    });
                }else console.log('Error updating display. Server Error: ' + xhr.status);
            }
        }
        
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send();
    }else{
        console.log('Nothing to update.');
    }
}

function reset_scroll_timers(){
    scroll_timers.forEach( function(timer, index) {
        window.clearInterval(timer);
    });

    scroll_timers = [];
    scroll_start('display');
}

//Initialize Display
scroll_start('display');

var ref_time = new Date();
window.setTimeout(function(){
    window.setInterval(function(){
        update_start('scroll-content');
        console.log('updated at' + (new Date));
    }, 1000 * 60 * 15)
}, (15 - ref_time.getMinutes() % 15) * 60 * 1000 + ref_time.getSeconds() * 1000 + ref_time.getMilliseconds());