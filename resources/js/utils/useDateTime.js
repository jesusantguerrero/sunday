import { format, isToday, isTomorrow, formatRelative, isThisWeek, isThisYear, isDate, differenceInCalendarDays } from "date-fns";
import { DateTime, Duration } from "luxon";
import { computed, ref} from "vue";

export  const formatHumanDate = (isoDate) => {
    try {
        if (!isDate(isoDate)) {
            return isoDate;
        }
        if (isToday(isoDate)) {
            return 'Today';
        } else if (isTomorrow(isoDate)) {
            return 'Tomorrow';
        } else if (isThisWeek(isoDate)) {
            return formatRelative(date.value, new Date()).replace(' at 12:00 AM', '');
        } else if (isThisWeek(isoDate)) {
            return format(date.value, 'iiii');
        } else if (isThisYear(isoDate)) {
            return format(isoDate, 'MMM dd')
        } else {
            return format(isoDate, 'MMM dd yyyy')
        }
    } catch (err) {
        return isoDate;
    }
}

export function useDateTime(dateRef) {
    const date = dateRef || ref(null)

    const formattedDate = computed(() => {
        return date.value && typeof date.value == 'object' ? toISO(date.value) : date.value;
    })

    const humanDate = computed(() => {
        const isoDate = date.value && typeof date.value == 'object' ? date.value : date.value;
       return formatHumanDate(isoDate)
    })

    const formatDate = (date, format) => {
       return DateTime.fromJSDate(date || new Date()).toFormat(format || "yyyy-MM-dd");
    }

    const toISO = (date) => {
       return date ? DateTime.fromJSDate(date).toISODate() :  ""
    }

    const formatDurationFromMs = (ms) => {
        return Duration.fromMillis(ms)
    }

    const getDateFromString = (dateValue) => {
        const date = dateValue ? dateValue.split("-") : null;
        return date ? new Date(date[0], date[1] - 1, date[2]) : null;
    }

    return {
        formattedDate,
        toISO,
        formatDate,
        formatDurationFromMs,
        getDateFromString,
        date: date.value,
        humanDate
    }
}
