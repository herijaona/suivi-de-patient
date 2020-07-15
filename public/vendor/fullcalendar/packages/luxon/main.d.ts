// d by dts-bundle v0.7.3-fork.1
// Dependencies for this module:
//   ../../../../../luxon
//   ../../../../../@fullcalendar/core

declare module '@fullcalendar/luxon' {
    import { DateTime as LuxonDateTime, Duration as LuxonDuration } from 'luxon';
    import { Calendar, Duration } from '@fullcalendar/core';
    export function toDateTime(date: Date, calendar: Calendar): LuxonDateTime;
    export function toDuration(duration: Duration, calendar: Calendar): LuxonDuration;
    const _default: import("@fullcalendar/core").PluginDef;
    export default _default;
}

