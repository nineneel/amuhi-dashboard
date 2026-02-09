@extends('layouts.app')

@section('title', 'Events Calendar')
@section('header', 'Events Calendar')

@section('page-header')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Events Calendar</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                <li class="breadcrumb-item">Calendar</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('events.index') }}" class="btn btn-light-brand">
                        <i class="feather-list me-2"></i>List View
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card stretch">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light" type="button" data-calendar-nav="prev">
                                <i class="feather-chevron-left"></i>
                            </button>
                            <button class="btn btn-light" type="button" data-calendar-nav="today">Today</button>
                            <button class="btn btn-light" type="button" data-calendar-nav="next">
                                <i class="feather-chevron-right"></i>
                            </button>
                            <span id="calendar-range" class="fw-semibold ms-2"></span>
                        </div>
                        <div class="btn-group" role="group">
                            <button class="btn btn-light" type="button" data-calendar-view="month">Month</button>
                            <button class="btn btn-light" type="button" data-calendar-view="week">Week</button>
                            <button class="btn btn-light" type="button" data-calendar-view="day">Day</button>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2 fs-13 text-muted">
                                <span class="badge bg-primary">&nbsp;</span>
                                <span>Upcoming</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 fs-13 text-muted">
                                <span class="badge bg-success">&nbsp;</span>
                                <span>Registered</span>
                            </div>
                        </div>
                    </div>

                    <div id="tui-calendar" class="calendar-shell"></div>

                    @if($events->isEmpty())
                        <div class="alert alert-light border mt-4 mb-0">
                            No events to display yet. Check back soon for upcoming schedules.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('vendors/css/tui-calendar.min.css') }}">
    <link rel="stylesheet" href="{{ url('vendors/css/tui-theme.min.css') }}">
    <link rel="stylesheet" href="{{ url('vendors/css/tui-time-picker.min.css') }}">
    <link rel="stylesheet" href="{{ url('vendors/css/tui-date-picker.min.css') }}">
    <style>
        .calendar-shell {
            height: 780px;
        }

        .tui-full-calendar-popup {
            font-family: inherit;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ url('vendors/js/tui-code-snippet.min.js') }}"></script>
    <script src="{{ url('vendors/js/tui-time-picker.min.js') }}"></script>
    <script src="{{ url('vendors/js/tui-date-picker.min.js') }}"></script>
    <script src="{{ url('vendors/js/tui-calendar.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const calendarElement = document.getElementById('tui-calendar');
            if (!calendarElement || !window.tui || !window.tui.Calendar) {
                return;
            }

            const calendar = new tui.Calendar('#tui-calendar', {
                defaultView: 'month',
                taskView: false,
                scheduleView: ['time'],
                useCreationPopup: false,
                useDetailPopup: true,
                calendars: [
                    {
                        id: 'events',
                        name: 'Events',
                        color: '#ffffff',
                        bgColor: '#4f46e5',
                        borderColor: '#4f46e5',
                    },
                    {
                        id: 'registered',
                        name: 'Registered',
                        color: '#ffffff',
                        bgColor: '#16a34a',
                        borderColor: '#16a34a',
                    },
                ],
            });

            const schedules = @json($calendarEvents);
            schedules.forEach(schedule => {
                schedule.calendarId = schedule.raw?.registered ? 'registered' : 'events';
                schedule.category = schedule.category || 'time';
            });
            calendar.createSchedules(schedules);

            const rangeElement = document.getElementById('calendar-range');
            const setRangeLabel = () => {
                if (!rangeElement || !window.moment) {
                    return;
                }

                const viewName = calendar.getViewName();
                const start = calendar.getDateRangeStart();
                const end = calendar.getDateRangeEnd();

                if (viewName === 'month') {
                    rangeElement.textContent = moment(start).format('MMMM YYYY');
                    return;
                }

                const startLabel = moment(start).format('DD MMM');
                const endLabel = moment(end).format('DD MMM YYYY');
                rangeElement.textContent = `${startLabel} - ${endLabel}`;
            };

            setRangeLabel();

            document.querySelectorAll('[data-calendar-nav]').forEach(button => {
                button.addEventListener('click', () => {
                    const direction = button.getAttribute('data-calendar-nav');
                    if (direction === 'prev') {
                        calendar.prev();
                    } else if (direction === 'next') {
                        calendar.next();
                    } else {
                        calendar.today();
                    }
                    setRangeLabel();
                });
            });

            document.querySelectorAll('[data-calendar-view]').forEach(button => {
                button.addEventListener('click', () => {
                    const view = button.getAttribute('data-calendar-view');
                    calendar.changeView(view, true);
                    document.querySelectorAll('[data-calendar-view]').forEach(item => {
                        item.classList.remove('btn-primary');
                        item.classList.add('btn-light');
                    });
                    button.classList.remove('btn-light');
                    button.classList.add('btn-primary');
                    setRangeLabel();
                });
            });

            const defaultButton = document.querySelector('[data-calendar-view="month"]');
            if (defaultButton) {
                defaultButton.classList.remove('btn-light');
                defaultButton.classList.add('btn-primary');
            }
        });
    </script>
@endpush
