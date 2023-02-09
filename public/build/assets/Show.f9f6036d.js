import p from"./TeamMemberManager.79c8c3cf.js";import{_}from"./AppLayout.61ea5a27.js";import d from"./DeleteTeamForm.af705fe1.js";import{J as f}from"./SectionBorder.a2811464.js";import u from"./UpdateTeamNameForm.3f8a935c.js";import{_ as x}from"./_plugin-vue_export-helper.cdc0426e.js";import{r as t,o,j as b,b as s,d as a,a as m,c as g,F as h,e as v}from"./app.e6e97f3c.js";import"./ActionMessage.573a1b0b.js";import"./ActionSection.d33c4220.js";import"./SectionTitle.e8cffaaf.js";import"./Button.477681d3.js";import"./ConfirmationModal.a1a61d14.js";import"./Modal.1dbb4fb9.js";import"./DangerButton.335aa55c.js";import"./DialogModal.d1e7aea7.js";import"./Label.93f1bb65.js";import"./InputError.a7480a18.js";import"./SecondaryButton.2123890a.js";const y={props:["team","availableRoles","permissions"],components:{AppLayout:_,DeleteTeamForm:d,JetSectionBorder:f,TeamMemberManager:p,UpdateTeamNameForm:u}},B=a("h2",{class:"text-xl font-semibold leading-tight text-gray-800"}," Team Settings ",-1),T={class:"py-10 mx-auto max-w-8xl sm:px-6 lg:px-8"};function k(F,N,e,j,w,C){const r=t("update-team-name-form"),n=t("team-member-manager"),i=t("jet-section-border"),l=t("delete-team-form"),c=t("app-layout");return o(),b(c,null,{header:s(()=>[B]),default:s(()=>[a("div",null,[a("div",T,[m(r,{team:e.team,permissions:e.permissions},null,8,["team","permissions"]),m(n,{class:"mt-10 sm:mt-0",team:e.team,"available-roles":e.availableRoles,"user-permissions":e.permissions},null,8,["team","available-roles","user-permissions"]),e.permissions.canDeleteTeam&&!e.team.personal_team?(o(),g(h,{key:0},[m(i),m(l,{class:"mt-10 sm:mt-0",team:e.team},null,8,["team"])],64)):v("",!0)])])]),_:1})}const Q=x(y,[["render",k]]);export{Q as default};