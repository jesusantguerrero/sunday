import Vue from "vue";
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'
import {
    DatePicker,
    Table,
    TableColumn,
    TimePicker,
    Dialog,
    Tooltip,
    Dropdown,
    DropdownItem,
    DropdownMenu,
    Select,
    Option,
    OptionGroup,
    Input,
    Popover,
    Time,
    Loading,
    Notification
} from "element-ui";

// configure language
locale.use(lang)

Vue.use(Loading);
Vue.use(DatePicker);
Vue.use(Tooltip);
Vue.use(TimePicker);
Vue.use(Dialog);
Vue.use(Dropdown);
Vue.use(DropdownItem);
Vue.use(DropdownMenu);
Vue.use(Select);
Vue.use(Option);
Vue.use(Input);
Vue.use(OptionGroup);
Vue.use(Popover);
Vue.use(TimePicker);
Vue.use(TableColumn);
Vue.use(Table);

Vue.prototype.$notify = Notification;
