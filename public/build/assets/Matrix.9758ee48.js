import{_ as M,B as U}from"./AppLayout.8ec9311d.js";import{B as j,P as A,a as P,L as E,s as J}from"./Viewer.be791c8f.js";import{S as N}from"./controls.94abbb5d.js";import{S as q}from"./index.5e032af4.js";import{J as z}from"./DialogModal.7ab1b676.js";import{J as G}from"./Button.2e6d22da.js";import{C as k,k as a,o as d,a as x,w as m,b as t,j as r,g as _,t as p,d as u,e as f,n as w,F as y,i as g,v,q as H,B as K}from"./app.3ce0088d.js";import{_ as Q}from"./plugin-vue_export-helper.21dcd24c.js";import{t as R,f as S}from"./index.590f4998.js";import"./ConfirmationModal.60162698.js";import"./Modal.09035abd.js";import"./DangerButton.6c27673a.js";import"./SecondaryButton.9fb1985c.js";import"./ItemGroupCell.b94df27d.js";import"./index.41fb7a9b.js";const W={components:{AppLayout:M,BoardSide:U,BoardItemContainer:j,ScheduleView:q,Promodoro:A,DialogModal:z,LinkFormModal:P,LinkViewer:E,PrimaryButton:G,ScheduleControls:N},props:{boards:{type:Array,default(){return[]}},todo:{type:[Array,Object],default(){return[]}},agenda:{type:[Array,Object],default(){return[]}},commitDate:{type:String,required:!0},standup:{type:Array,default(){return[]}},links:{type:Array,default(){return[]}},committed:{type:[Array,Object],default(){return[]}},settings:{type:Object,default(){return{}}}},data(){return{modes:["committed","done","scheduled","delegated","deleted","backlog"],selectedStage:"",modeSelected:"inbox",promodoroColor:"red",standupSummary:[],localCommitDate:new Date,isLoading:!1,isStandupOpen:!1,isLinkFormOpen:!1,linkData:{},tracker:null}},watch:{localCommitDate(e,s){s&&e.toISOString().slice(0,10)!=s.toISOString().slice(0,10)&&this.getCommitsByDate()}},computed:{hasCommited(){return this.todo.filter(e=>e.done).length},showTodo(){return["all","inbox"].includes(this.modeSelected)},showCommitted(){return["all","committed"].includes(this.modeSelected)},stages(){return k.exports.uniq(this.todo.map(e=>e.stage))},inbox(){const e=this.selectedStage?this.todo.filter(s=>s.stage==this.selectedStage):this.todo;return k.exports.orderBy(e,["priority","stage","title"])}},mounted(){!this.standup.length&&this.todo.length&&(this.standupSummary={...this.todo},this.isStandupOpen=!0)},created(){this.setCommitDate()},methods:{setCommitDate(){let e=new Date;this.commitDate&&(e=this.commitDate.split("-"),e=R(new Date(e[0],e[1]-1,e[2]))),this.localCommitDate=e},completeDay(){this.isLoading=!0;const e=S(J(new Date,1),"yyyy-MM-dd"),s=S(new Date,"yyyy-MM-dd");let i=this.todo.filter(c=>c.done);i=i.map(c=>(c.commit_date=e,c)),i.forEach(async c=>{await this.updateItem(c)}),this.updateDaily(s),this.isStandupOpen=!1,this.isLoading=!1,this.$inertia.reload({preserveScroll:!0})},getCommitsByDate(){const e=`?commit-date=${this.localCommitDate.toISOString().slice(0,10)}`;this.$inertia.replace(`/${e}`,{only:["committed"],preserveState:!0})},updateItem(e){const s=e.id?"PUT":"POST",i=e.id?`/${e.id}`:"";axios({url:`/items${i}`,method:s,data:e}).then(()=>(this.$inertia.reload({preserveScroll:!0}),!0))},updateDaily(e){axios({url:"standups",method:"post",data:{date:e}})},closeLinkForm(){this.linkData={},this.isLinkFormOpen=!1},openLinkForm(e){this.linkData=e,this.isLinkFormOpen=!0},onLinkSaved(){this.closeLinkForm(),this.$inertia.reload({preserveScroll:!0})},setTaskToTimer(e){this.$refs.Promodoro.setTask(e)}}},X={class:""},Y={class:"flex flex-col mx-auto max-w-8xl sm:pr-6 lg:pr-8 md:flex-row"},Z={class:"pt-12 w-100 md:w-7/12 lg:w-8/12 md:mx-4"},$={class:"flex flex-col justify-between mx-2 md:flex-row md:mr-2 md:ml-0"},tt=t("span",{class:"text-3xl font-bold"}," Today's Todos ",-1),et={class:"flex items-center"},ot={class:"w-40 mr-2"},st={slot:"singleLabel","slot-scope":"props"},nt={class:"option__title"},it=t("i",{class:"mr-2 fa fa-briefcase"},null,-1),lt={slot:"option","slot-scope":"props"},at={class:"option__desc"},rt={class:"option__title"},dt=t("i",{class:"mr-2 fa fa-briefcase"},null,-1),mt={class:"h-10 bg-purple-700 rounded-lg controls"},ct=["onClick"],pt={class:"pt-12 w-100 md:w-5/12 lg:w-4/12 md:ml-4"},ut=t("span",{class:"ml-2 text-3xl font-bold"}," Tools ",-1),_t={class:"mt-5 section-card committed"},ht={class:"section-card committed"},ft={class:"flex justify-between font-bold text-white bg-blue-400"},yt=t("span",null," Links ",-1),gt=t("i",{class:"fa fa-plus"},null,-1),bt=[gt],kt={class:"text-gray-600 bg-blue-400 body"},xt={class:"section-card committed"},wt={class:"flex justify-between font-bold text-white bg-blue-400"},vt=t("span",null," Agenda ",-1),St=_(" Go to Planner "),Ct={class:"text-gray-600 bg-blue-400 body"},Dt={class:"mr-2 font-bold"},Lt={class:"capitalize"},Ot=_(" Today's Standup "),Tt={class:"checkbox-label"},Ft=["onChange","id","onUpdate:modelValue"],It={class:"font-bold"},Bt=_(" Complete Day ");function Vt(e,s,i,c,n,l){const C=a("multiselect"),D=a("schedule-controls"),b=a("board-item-container"),L=a("promodoro"),O=a("link-viewer"),T=a("inertia-link"),F=a("primary-button"),I=a("dialog-modal"),B=a("link-form-modal"),V=a("app-layout");return d(),x(V,{boards:i.boards},{default:m(()=>[t("div",X,[t("div",Y,[t("div",Z,[t("div",$,[tt,t("div",et,[t("div",ot,[r(C,{modelValue:n.selectedStage,"onUpdate:modelValue":s[0]||(s[0]=o=>n.selectedStage=o),ref:"input","show-labels":!1,placeholder:"Filter by stage",options:l.stages,class:"w-full"},{default:m(()=>[t("template",st,[t("span",nt,[it,_(" "+p(e.props.option),1)])]),t("template",lt,[t("div",at,[t("span",rt,[dt,_(" "+p(e.props.option),1)])])])]),_:1},8,["modelValue","options"])]),t("div",mt,[(d(!0),u(y,null,f(n.modes,o=>(d(),u("button",{key:o,onClick:h=>n.modeSelected=o,class:w([{"bg-purple-400":o==n.modeSelected},"h-full px-8 text-white capitalize rounded-lg"])},p(o),11,ct))),128))])])]),g(r(b,{title:"Commited",tasks:i.committed,onUpdateItem:l.updateItem},{default:m(()=>[t("template",null,[n.localCommitDate?(d(),x(D,{key:0,modelValue:n.localCommitDate,"onUpdate:modelValue":s[1]||(s[1]=o=>n.localCommitDate=o)},null,8,["modelValue"])):H("",!0)])]),_:1},8,["tasks","onUpdateItem"]),[[v,l.showCommitted]]),g(r(b,{title:"To Do",tasks:l.inbox,tracker:n.tracker,onUpdateItem:l.updateItem,onItemClicked:l.setTaskToTimer},null,8,["tasks","tracker","onUpdateItem","onItemClicked"]),[[v,l.showTodo]])]),t("div",pt,[ut,t("div",_t,[t("div",{class:w(`bg-${n.promodoroColor}-400 text-gray-600 font-bold px-0`)},[r(L,{ref:"Promodoro",settings:i.settings,tracker:n.tracker,"timer-color":n.promodoroColor,tasks:i.todo},null,8,["settings","tracker","timer-color","tasks"])],2)]),t("div",ht,[t("header",ft,[yt,t("button",{class:"text-white bg-transparent",onClick:s[2]||(s[2]=o=>n.isLinkFormOpen=!n.isLinkFormOpen)},bt)]),t("div",kt,[r(O,{links:i.links,onEdit:l.openLinkForm},null,8,["links","onEdit"])])]),t("div",xt,[t("header",wt,[vt,r(T,{class:"text-white bg-transparent",href:"/planner"},{default:m(()=>[St]),_:1})]),t("div",Ct,[(d(!0),u(y,null,f(i.agenda,o=>(d(),u("div",{class:"p-2 text-white rounded-md cursor-pointer hover:bg-blue-500",key:`event-${o.id}`},[t("span",Dt,p(o.time),1),t("span",Lt,p(o.title),1)]))),128))])])])]),r(I,{show:n.isStandupOpen,onClose:s[4]||(s[4]=o=>n.isStandupOpen=!1)},{title:m(()=>[Ot]),content:m(()=>[t("div",null,[(d(!0),u(y,null,f(n.standupSummary,o=>(d(),u("p",{key:`task-${o.id}`},[t("label",Tt,[g(t("input",{type:"checkbox",onChange:h=>l.updateItem(o),name:"",id:o.id,"onUpdate:modelValue":h=>o.done=h},null,40,Ft),[[K,o.done]]),t("span",It," ["+p(o.stage)+"] ",1),t("span",null,p(o.title),1)])]))),128))])]),footer:m(()=>[r(F,{onClick:s[3]||(s[3]=o=>l.completeDay())},{default:m(()=>[Bt]),_:1})]),_:1},8,["show"]),r(B,{"record-data":n.linkData,"is-open":n.isLinkFormOpen,onSaved:l.onLinkSaved,onCancel:s[5]||(s[5]=o=>n.isLinkFormOpen=!1)},null,8,["record-data","is-open","onSaved"])])]),_:1},8,["boards"])}var Wt=Q(W,[["render",Vt]]);export{Wt as default};