import{_ as c}from"./plugin-vue_export-helper.21dcd24c.js";import{o as s,d as a,b as t,t as r,F as p,e as m,q as o,A as b,g as h,n as x}from"./app.3ce0088d.js";const f={props:{info:{type:Object,required:!0}}},y={class:"w-4/12 mx-2 p-5 bg-white border-gray-300 border-1 shadow-md rounded-md"},v={class:"mb-2"},k={class:"remark_info text-cool-gray-600 font-extrabold text-xl"},g={key:0,class:"links_container mt-3"},$=["href"];function w(n,i,e,u,_,l){return s(),a("div",y,[t("h5",v,r(e.info.title),1),t("div",k,r(e.info.value),1),e.info&&e.info.links?(s(),a("div",g,[(s(!0),a(p,null,m(e.info.links,d=>(s(),a("a",{class:"text-purple-400 text-sm font-bold",href:d.ref,key:d.ref},r(d.label),9,$))),128))])):o("",!0)])}var ae=c(f,[["render",w]]);const C={props:{plan:{type:Object,required:!0}},computed:{status(){return this.plan.status&&this.plan.status.toLowerCase()}}},S={class:"bg-white px-5 py-10 mb-5 mx-2 rounded-md shadow-md rounded-md flex"},D={class:"w-4/12"},L={class:"mx-auto"},q={class:"prose prose-xl"},B=t("h5",{class:"mb-2"}," Plan Details",-1),O={class:"text-3xl"},P={class:"text-purple-600 font-bold"},V=t("div",{class:"text-2xl text-gray-400"}," Your plan information ",-1),N={class:"text-center w-4/12"},j={class:"prose prose-xl px-5 py-2 my-2 rounded-md mx-auto"},A={key:0},F=t("h4",null," Days left ",-1),R=b('<div class="mt-10 mb-2"><h5> Plan Details</h5><p class="text-2x1 text-gray-400"><span><i class="fa fa-users"></i> 1 member(s) </span><span class="ml-2"><i class="fa fa-users"></i> 1 team(s) </span></p></div>',1),T={class:"text-center w-4/12"};function z(n,i,e,u,_,l){return s(),a("div",S,[t("div",D,[t("div",L,[t("div",q,[B,t("div",O,[t("span",P,r(e.plan.name),1),V])])])]),t("div",N,[t("div",j,[e.plan.trials_ends_at?(s(),a("div",A,[F,t("p",null,r(e.plan.trials_ends_at),1)])):o("",!0),R])]),t("div",T,[l.status=="active"?(s(),a("button",{key:0,class:"bg-cool-gray-500 text-white px-5 py-2 inline-block rounded-md",onClick:i[0]||(i[0]=d=>n.$emit("suspend"))}," Suspend ")):o("",!0),l.status=="suspended"?(s(),a("button",{key:1,class:"bg-green-500 text-white px-5 py-2 inline-block rounded-md",onClick:i[1]||(i[1]=d=>n.$emit("reactivate"))}," Reactivate ")):o("",!0),["active","suspended"].includes(l.status)?(s(),a("button",{key:2,class:"bg-red-500 text-white px-5 py-2 inline-block rounded-md",onClick:i[2]||(i[2]=d=>n.$emit("cancel"))}," cancel ")):o("",!0)])])}var ne=c(C,[["render",z]]);const E={props:{plan:{type:Object,required:!0},isCurrent:{type:Boolean,default:!1},subscribeLink:{type:String,required:!0},contactLink:{type:String},subscribeLabel:{type:String,default:"subscribe"}},mounted(){const n=this;paypal.Buttons({createSubscription(i,e){return e.subscription.create({plan_id:n.plan.paypal_plan_id})},onApprove(i,e){i.plan_id=n.plan.paypal_plan_id,n.createSubscription(i)}}).render(this.$refs.buttonsContainer)},methods:{createSubscription(n){console.log(n),axios({method:"POST",url:`/v2/subscriptions/${n.subscriptionID}/save`,data:n}).then(()=>{this.fireworks()})}}},I={class:"prose prose-xl"},U={class:"text-center"},Y={key:0,class:"rounded-md text-purple-600 px-1 py-1 text-xs"},G={class:"px-5 py-2 my-2 rounded-md"},H={class:"text-5xl text-center"},J={class:"font-extrabold"},K=t("small",{class:"text-md"}," USD ",-1),M=t("div",{class:"mt-5"},[t("div",{class:"prose prose-md"}," Lorem ipsum dolor sit amet consectetur adipisicing elit. Recusandae excepturi assumenda minus ad voluptates quo! Voluptates, sint amet obcaecati quaerat tempora exercitationem cum ullam ab quae. Optio labore debitis voluptas? ")],-1),Q={key:0,class:"text-center"},W=["href"],X={key:1,ref:"buttonsContainer"},Z=["href"];function ee(n,i,e,u,_,l){return s(),a("div",{class:x(["bg-white w-4/12 px-5 py-10 mb-5 mx-6 shadow-md rounded-md",{"border-purple-400 border-2":e.isCurrent}])},[t("div",I,[t("h3",U,[h(r(e.plan.name)+" ",1),e.isCurrent?(s(),a("div",Y," Current Plan ")):o("",!0)])]),t("div",G,[t("h2",H,[t("span",J,r(e.plan.quantity),1),K]),M]),e.isCurrent?o("",!0):(s(),a("div",Q,[e.contactLink?(s(),a("a",{key:0,class:"border-2 border-purple-500 bg-white text-blue-500 px-5 py-2 inline-block rounded-md",href:e.contactLink}," Contact Sales ",8,W)):o("",!0),e.plan.paypal_plan_id?(s(),a("div",X,null,512)):(s(),a("a",{key:2,class:"bg-gray-400 text-white px-5 py-2 inline-block rounded-sm w-full",href:e.subscribeLink},r(e.subscribeLabel),9,Z))]))],2)}var ie=c(E,[["render",ee]]);export{ae as D,ne as a,ie as b};