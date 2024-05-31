import { format, parseISO } from "date-fns";

export const isSameDate = (date1, date2) => {
    return format(date1, 'yyyy-MM-dd') === format(date2, 'yyyy-MM-dd')
}

export const stringToDate = (dateString) => {
    const date =  parseISO(dateString+"T00:00")
    return date;
}
