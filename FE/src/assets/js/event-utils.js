import moment from 'moment';

let eventGuid = 0;
let todayStr = moment(new Date()).format('YYYY-MM-DD'); // YYYY-MM-DD of today

export const INITIAL_EVENTS = [
  {
    id: createEventId(),
    title: 'All-day event',
    start: '2021-12-07',
    allDay: true,
    color: '#FF746B',
    textColor: '#ffffff',
    edited: true,
    personal: 1
  },
  {
    id: createEventId(),
    title: 'Timed event 1',
    start: todayStr + 'T06:00:00',
    end: todayStr + 'T07:30:00',
    color: '#FF746B',
    textColor: '#ffffff',
    edited: true,
    personal: 1
  },
  {
    id: createEventId(),
    title: 'Timed event 11',
    start: todayStr + 'T06:00:00',
    end: todayStr + 'T07:30:00',
    color: '#FF746B',
    textColor: '#ffffff',
    edited: true,
    personal: 1
  },
  {
    id: createEventId(),
    title: 'Timed event 2',
    start: '2021-10-07T12:00:00',
    end: '2021-10-07T16:00:00',
    edited: true,
    color: '#008000',
    textColor: '#ffffff',
    personal: 2
  },
  {
    id: createEventId(),
    title: 'Timed event 2',
    start: todayStr + 'T19:00:00',
    end: todayStr + 'T20:00:00',
    edited: true,
    color: '#008000',
    textColor: '#ffffff',
    personal: 2
  },
  {
    id: createEventId(),
    title: 'Timed event 21',
    start: todayStr + 'T09:00:00',
    end: todayStr + 'T11:30:00',
    edited: true,
    color: '#008000',
    textColor: '#ffffff',
    personal: 2
  },
  {
    id: createEventId(),
    title: 'Timed event 22',
    start: todayStr + 'T13:00:00',
    end: todayStr + 'T14:00:00',
    edited: true,
    color: '#DDA327',
    textColor: '#ffffff',
    personal: 4
  },
  {
    id: createEventId(),
    title: 'Timed event 3',
    start: todayStr + 'T15:00:00',
    end: todayStr + 'T16:00:00',
    edited: true,
    color: '#59A5FF',
    textColor: '#ffffff',
    personal: 3
  },
  {
    id: createEventId(),
    title: 'Timed event 31',
    start: todayStr + 'T08:00:00',
    end: todayStr + 'T10:00:00',
    edited: true,
    color: '#59A5FF',
    textColor: '#ffffff',
    personal: 3
  }
];

export const PERSONAL = [
  {
    id: 1,
    name: 'Test 1',
    colorCalendar: '#FF746B'
  },
  {
    id: 2,
    name: 'Test 2',
    colorCalendar: '#008000'
  },
  {
    id: 3,
    name: 'Test 3',
    colorCalendar: '#59A5FF'
  },
  {
    id: 4,
    name: 'Test 4',
    colorCalendar: '#DDA327'
  }
];

export function createEventId() {
  return String(eventGuid++);
}

export function getEvent() {
  let list = INITIAL_EVENTS.map((x) => {
    x.allDay = true;
  });

  return list;
}
