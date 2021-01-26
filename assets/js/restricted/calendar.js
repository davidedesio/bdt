import {Calendar} from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import itLocale from '@fullcalendar/core/locales/it';

document.addEventListener("DOMContentLoaded", () => {

    var calendarEl = document.getElementById("calendar-holder");

    if (calendarEl){

        var eventsUrl = calendarEl.dataset.eventsUrl;
        var calendarRole = calendarEl.dataset.calendarRole;
        var eventSourceFilters = {
            "calendar-role": calendarRole,
            "activity-type": "",
            "activity-category": "",
            "accepted": "",
        }
        var eventSource = {
            url: eventsUrl,
            method: "POST",
            extraParams: {
                filters: JSON.stringify(eventSourceFilters)
            },
            failure: () => {
                // alert("There was an error while fetching FullCalendar!");
            },
        }

        var calendar = new Calendar(calendarEl, {
            initialView: "dayGridMonth", //"dayGridMonth,timeGridWeek,timeGridDay"
            eventSources: [eventSource],
            loading: function( isLoading, view ) {
                if(isLoading) {// isLoading gives boolean value
                    //show your loader here
                    $(".hourglass-wrapper").fadeIn(500)
                } else {
                    //hide your loader here
                    $(".hourglass-wrapper").fadeOut(1500)
                }
            },
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: false
            },
            height: "auto",
            locale: itLocale,
            plugins: [interactionPlugin, dayGridPlugin], // https://fullcalendar.io/docs/plugin-index
            timeZone: "UTC",
            eventDidMount: function(info) {
                if(calendarRole==="ADMIN"){
                    var id = info.event._def.publicId;
                    var el = info.el;
                    $(el).append("<div class='fc-event-action show'><i class='fa fa-pen'></i>&nbsp;Modifica</div>")
                    $(el).append("<div class='fc-event-action del' onclick='event.preventDefault();event.stopPropagation();del("+id+");'><i class='fa fa-trash'></i>&nbsp;Elimina</div>");
                }
            }
        });
        calendar.render();

        $("#activity_type_filter, #activity_category_filter, #accepted_filter").change(function(){
            applyFilters();
        })

        var applyFilters = function(){
            var activtyType = $("#activity_type_filter").val();
            var activityCategory = $("#activity_category_filter").val();
            var accpetedFilter = $("#accepted_filter").val();
            var newEventSourceFilters = {
                "calendar-role": calendarRole,
                "activity-type": activtyType,
                "activity-category": activityCategory,
                "accepted": accpetedFilter
            }

            eventSource.extraParams = {
                filters: JSON.stringify(newEventSourceFilters)
            }

            calendar.removeAllEvents();
            calendar.removeAllEventSources();
            calendar.addEventSource(eventSource);
        }
    }



});