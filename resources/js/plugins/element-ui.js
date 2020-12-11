import Vue from "vue";
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'
import {
    DatePicker,
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
    Notification
} from "element-ui";

// configure language
locale.use(lang)

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

Vue.prototype.$notify = Notification;
