import{J as k}from"./ActionMessage.573a1b0b.js";import{J as x}from"./ActionSection.d33c4220.js";import{J as j}from"./Button.477681d3.js";import{J as B}from"./DialogModal.d1e7aea7.js";import{J as b,a as L}from"./InputError.a7480a18.js";import{J as S}from"./SecondaryButton.2123890a.js";import{r as i,o as n,j as J,b as e,c as a,F as C,f as M,e as V,d as o,a as c,V as O,n as E,g as r,t as d}from"./app.e6e97f3c.js";import{_ as N}from"./_plugin-vue_export-helper.cdc0426e.js";import"./SectionTitle.e8cffaaf.js";import"./Modal.1dbb4fb9.js";const D={props:["sessions"],components:{JetActionMessage:k,JetActionSection:x,JetButton:j,JetDialogModal:B,JetInput:b,JetInputError:L,JetSecondaryButton:S},data(){return{confirmingLogout:!1,form:this.$inertia.form({_method:"DELETE",password:""},{bag:"logoutOtherBrowserSessions"})}},methods:{confirmLogout(){this.form.password="",this.confirmingLogout=!0,setTimeout(()=>{this.$refs.password.focus()},250)},logoutOtherBrowserSessions(){this.form.post("/user/other-browser-sessions",{preserveScroll:!0}).then(h=>{this.form.hasErrors()||(this.confirmingLogout=!1)})}}},I=r(" Browser Sessions "),T=r(" Manage and logout your active sessions on other browsers and devices. "),z=o("div",{class:"max-w-xl text-sm text-gray-600"}," If necessary, you may logout of all of your other browser sessions across all of your devices. If you feel your account has been compromised, you should also update your password. ",-1),F={key:0,class:"mt-5 space-y-6"},K={class:"flex items-center"},A={key:0,fill:"none","stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",viewBox:"0 0 24 24",stroke:"currentColor",class:"w-8 h-8 text-gray-500"},H=o("path",{d:"M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"},null,-1),P=[H],U={key:1,xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24","stroke-width":"2",stroke:"currentColor",fill:"none","stroke-linecap":"round","stroke-linejoin":"round",class:"w-8 h-8 text-gray-500"},q=o("path",{d:"M0 0h24v24H0z",stroke:"none"},null,-1),G=o("rect",{x:"7",y:"4",width:"10",height:"16",rx:"1"},null,-1),Q=o("path",{d:"M11 5h2M12 17v.01"},null,-1),R=[q,G,Q],W={class:"ml-3"},X={class:"text-sm text-gray-600"},Y={class:"text-xs text-gray-500"},Z={key:0,class:"font-semibold text-green-500"},$={key:1},oo={class:"flex items-center mt-5"},so=r(" Logout Other Browser Sessions "),eo=r(" Done. "),to=r(" Logout Other Browser Sessions "),ro=r(" Please enter your password to confirm you would like to logout of your other browser sessions across all of your devices. "),no={class:"mt-4"},io=r(" Nevermind "),ao=r(" Logout Other Browser Sessions ");function co(h,l,u,lo,t,_){const m=i("jet-button"),p=i("jet-action-message"),f=i("jet-input"),g=i("jet-input-error"),w=i("jet-secondary-button"),y=i("jet-dialog-modal"),v=i("jet-action-section");return n(),J(v,null,{title:e(()=>[I]),description:e(()=>[T]),content:e(()=>[z,u.sessions.length>0?(n(),a("div",F,[(n(!0),a(C,null,M(u.sessions,s=>(n(),a("div",K,[o("div",null,[s.agent.is_desktop?(n(),a("svg",A,P)):(n(),a("svg",U,R))]),o("div",W,[o("div",X,d(s.agent.platform)+" - "+d(s.agent.browser),1),o("div",null,[o("div",Y,[r(d(s.ip_address)+", ",1),s.is_current_device?(n(),a("span",Z,"This device")):(n(),a("span",$,"Last active "+d(s.last_active),1))])])])]))),256))])):V("",!0),o("div",oo,[c(m,{onClick:_.confirmLogout},{default:e(()=>[so]),_:1},8,["onClick"]),c(p,{on:t.form.recentlySuccessful,class:"ml-3"},{default:e(()=>[eo]),_:1},8,["on"])]),c(y,{show:t.confirmingLogout,onClose:l[2]||(l[2]=s=>t.confirmingLogout=!1)},{title:e(()=>[to]),content:e(()=>[ro,o("div",no,[c(f,{type:"password",class:"block w-3/4 mt-1",placeholder:"Password",ref:"password",modelValue:t.form.password,"onUpdate:modelValue":l[0]||(l[0]=s=>t.form.password=s),onKeyup:O(_.logoutOtherBrowserSessions,["enter","native"])},null,8,["modelValue","onKeyup"]),c(g,{message:t.form.errors.password,class:"mt-2"},null,8,["message"])])]),footer:e(()=>[c(w,{onClick:l[1]||(l[1]=s=>t.confirmingLogout=!1)},{default:e(()=>[io]),_:1}),c(m,{class:E(["ml-2",{"opacity-25":t.form.processing}]),onClick:_.logoutOtherBrowserSessions,disabled:t.form.processing},{default:e(()=>[ao]),_:1},8,["onClick","class","disabled"])]),_:1},8,["show"])]),_:1})}const ko=N(D,[["render",co]]);export{ko as default};