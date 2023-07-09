@extends('layouts.app')

@section('content')

    <section style="height: 100%">
        <div class="container-fluid py-5">
            <div id='calendar'></div>
            <div id='calendar_list'></div>
        </div>
    </section>
    @if((new \Jenssegers\Agent\Agent())->isDesktop())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let calendarEl = document.getElementById('calendar');
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    timeZone: 'Europe/Bratislava',
                    firstDay: 1,
                    @if((new \Jenssegers\Agent\Agent())->isMobile())
                    initialView: 'dayGridMonth',
                    progressiveEventRendering: true,
                    titleFormat: {day: 'numeric', month: 'numeric'},
                    headerToolbar: {
                        start: 'title', // will normally be on the left. if RTL, will be on the right
                        center: '',
                        end: 'today prev,next' // will normally be on the right. if RTL, will be on the left
                    },
                    @else
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        start: 'title', // will normally be on the left. if RTL, will be on the right
                        center: '',
                        end: 'today dayGridMonth,timeGridWeek,timeGridDay,listWeek prev,next' // will normally be on the right. if RTL, will be on the left
                    },
                    @endif
                    nowIndicator: true,
                    dayMaxEvents: true,
                    editable: true,
                    eventDurationEditable: false,
                    themeSystem: 'bootstrap5',
                    eventLongPressDelay: 1000,
                    @if(app()->getLocale() == 'sk')
                    locale: 'sk',
                    @endif
                    height: 'auto',
                    eventDidMount: function (info) {
                        if (info.event.extendedProps.description) {
                            const tooltip = new bootstrap.Tooltip(info.el, {
                                title: info.event.extendedProps.description,
                                placement: 'top',
                                trigger: 'hover',
                                container: 'body'
                            })
                        }
                    },
                    eventDrop: function (info) {
                        axios.post('/calendar/update_task_time/' + info.event.id, null, {
                            params: {
                                time: info.event.start.toISOString(),
                            }
                        })
                    },
                    eventClick: function (info) {
                        if (info.event.id) {
                            let hash_id = info.event.id;
                            window.location.href = "/tasks/" + hash_id;
                        }
                    },
                    events: '{{route('calendar.data_feed')}}'
                });
                calendar.render();
            });
        </script>
    @endif
    @if((new \Jenssegers\Agent\Agent())->isMobile())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let calendarEl = document.getElementById('calendar');
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    timeZone: 'Europe/Bratislava',
                    firstDay: 1,
                    progressiveEventRendering: true,
                    initialView: 'dayGridMonth',
                    titleFormat: {month: 'long'},
                    headerToolbar: {
                        start: 'title', // will normally be on the left. if RTL, will be on the right
                        center: '',
                        end: 'today prev,next' // will normally be on the right. if RTL, will be on the left
                    },
                    nowIndicator: true,
                    dayMaxEvents: true,
                    editable: false,
                    eventDurationEditable: false,
                    themeSystem: 'bootstrap5',
                    eventLongPressDelay: 1000,
                    @if(app()->getLocale() == 'sk')
                    locale: 'sk',
                    @endif
                    height: 'auto',
                    dateClick: function (info) {
                        var clickedDate = info.date;
                        calendarList.gotoDate(clickedDate);
                    },
                    eventClick: function (info) {
                        var clickedDate = info.event.start;
                        calendarList.gotoDate(clickedDate);
                    },
                    events: '{{route('calendar.data_feed')}}'
                });
                calendar.render();
            });

            let calendarEl = document.getElementById('calendar_list');
            let calendarList = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'Europe/Bratislava',
                firstDay: 1,
                progressiveEventRendering: true,
                initialView: 'listDay',
                titleFormat: {day: 'numeric'},
                headerToolbar: {
                    start: 'title', // will normally be on the left. if RTL, will be on the right
                    center: '',
                    end: '' // will normally be on the right. if RTL, will be on the left
                },
                nowIndicator: true,
                dayMaxEvents: true,
                editable: false,
                eventDurationEditable: false,
                themeSystem: 'bootstrap5',
                eventLongPressDelay: 1000,
                @if(app()->getLocale() == 'sk')
                locale: 'sk',
                @endif
                height: 'auto',
                eventClick: function (info) {
                    if (info.event.id) {
                        let hash_id = info.event.id;
                        window.location.href = "/tasks/" + hash_id;
                    }
                },
                events: '{{route('calendar.data_feed')}}'
            });
            calendarList.render();
        </script>
    @endif

@endsection
