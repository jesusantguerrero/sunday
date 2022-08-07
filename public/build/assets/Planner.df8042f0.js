import{_ as u,B as p}from"./AppLayout.8ec9311d.js";import{S as _}from"./controls.94abbb5d.js";import{S as f}from"./index.5e032af4.js";import{I as h}from"./ItemModal.9ebaa974.js";import{_ as b}from"./plugin-vue_export-helper.21dcd24c.js";import{f as y}from"./index.590f4998.js";import{k as n,o as v,a as x,w,b as e,j as i}from"./app.3ce0088d.js";import"./ConfirmationModal.60162698.js";import"./Modal.09035abd.js";import"./DangerButton.6c27673a.js";import"./SecondaryButton.9fb1985c.js";import"./DialogModal.7ab1b676.js";import"./Button.2e6d22da.js";import"./index.41fb7a9b.js";import"./ItemGroupCell.b94df27d.js";const g={components:{AppLayout:u,BoardSide:p,ScheduleView:f,ScheduleControls:_,ItemModal:h},props:{boards:{type:Array,default(){return[]}},users:{type:Array,default(){return[]}},scheduled:{type:[Array,Object],default(){return[]}},date:{type:String},boardTypes:{type:Array,default(){return[]}},boardTemplates:{type:Array,default(){return[]}}},provide(){return{users:this.users}},data(){return{modes:["daily","weekly","monthly","quarter"],modeSelected:"daily",promodoroColor:"red",boardType:"",isLoading:!1,openedItem:{},isItemModalOpen:!1}},computed:{localDate(){return new Date(this.date)}},methods:{setCommitDate(){let s=null;s=this.date.split("-"),s=new Date(this.date),this.localDate=s},getParams(s){return`?date=${y(s,"yyyy-MM-dd")}`},getCommitsByDate(s){if(s){debugger;const t=this.getParams(s);this.$inertia.visit(`/planner${t}`,{only:["scheduled","date"]})}},openItem(s={},t="event"){this.isItemModalOpen=!0,this.openedItem=s,this.boardType=t},onItemSaved(){this.$nextTick(()=>{this.isItemModalOpen=!1,this.$inertia.reload(`/planner${this.params}`,{only:["scheduled"],preserveState:!0})})}}},I={class:""},S={class:"flex flex-col mx-auto max-w-8xl sm:pr-6 lg:pr-8 md:flex-row"},k={class:"pt-12 w-100 md:w-full md:mx-4"},C={class:"flex flex-col justify-between mx-2 md:flex-row md:mr-2 md:ml-6"},D=e("span",{class:"text-3xl font-bold"}," Planner ",-1),A={class:"mt-5 md:ml-6"},M={class:"hidden pt-12 w-100"},O=e("span",{class:"ml-2 text-3xl font-bold"}," Fast Access ",-1),j={class:"mt-5 section-card committed"},B={class:"flex justify-between font-bold text-white bg-blue-400"},T=e("span",null," Dailies ",-1),P=e("i",{class:"fa fa-plus"},null,-1),L=[P],F=e("div",{class:"text-gray-600 bg-blue-400 body"},null,-1),N={class:"mt-5 section-card committed"},V={class:"flex justify-between font-bold text-white bg-blue-400"},$=e("span",null," Habits ",-1),q=e("i",{class:"fa fa-plus"},null,-1),E=[q],H=e("div",{class:"text-gray-600 bg-blue-400 body"},null,-1),J={class:"mt-5 section-card committed"},z={class:"flex justify-between font-bold text-white bg-blue-400"},G=e("span",null," Notes ",-1),K=e("i",{class:"fa fa-plus"},null,-1),Q=[K],R=e("div",{class:"text-gray-600 bg-blue-400 body"},null,-1);function U(s,t,a,W,l,o){const r=n("schedule-view"),m=n("item-modal"),c=n("app-layout");return v(),x(c,{boards:a.boards},{default:w(()=>[e("div",I,[e("div",S,[e("div",k,[e("div",C,[e("div",null,[D,e("button",{class:"font-bold text-white bg-purple-400 btn",onClick:t[0]||(t[0]=d=>o.openItem())}," Add Event ")])]),e("div",A,[i(r,{value:o.localDate,onInput:o.getCommitsByDate,modes:l.modes,schedule:a.scheduled,"link-fields":{url_id:"Join",url_subject:"See meet"},"id-field":"","time-field":"","date-field":"due_date","time-end-field":"","date-end-field":"","title-field":""},null,8,["value","onInput","modes","schedule"])])]),e("div",M,[O,e("div",j,[e("header",B,[T,e("button",{class:"text-white bg-transparent",onClick:t[1]||(t[1]=d=>o.openItem({},"daily"))},L)]),F]),e("div",N,[e("header",V,[$,e("button",{class:"text-white bg-transparent",onClick:t[2]||(t[2]=d=>o.openItem({},"habit"))},E)]),H]),e("div",J,[e("header",z,[G,e("button",{class:"text-white bg-transparent",onClick:t[3]||(t[3]=d=>s.isLinkFormOpen=!s.isLinkFormOpen)},Q)]),R])])]),i(m,{onCancel:t[4]||(t[4]=d=>l.isItemModalOpen=!1),onSaved:o.onItemSaved,boards:a.boards,type:l.boardType,"record-data":l.openedItem,"is-open":l.isItemModalOpen},null,8,["onSaved","boards","type","record-data","is-open"])])]),_:1},8,["boards"])}var ue=b(g,[["render",U]]);export{ue as default};