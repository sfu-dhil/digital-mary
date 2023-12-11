(()=>{"use strict";(()=>{class e{constructor(e){try{this.el=e,this.summary=e.querySelector("summary"),this.content=this.makeContent(),this.animation=null,this.state=[0],this.easing=this.getCustomProp("easing","ease-in-out"),this.duration=this.convertDuration(this.getCustomProp("duration","400").trim()),this.startHeight=null,this.endHeight=null,this.init()}catch(e){console.log(`${e}`)}}init(){this.el.setAttribute("aria-expanded",this.el.open),this.el.classList.add("accordion"),this.summary.addEventListener("click",(e=>this.onClick(e)))}getCustomProp(e,t){let i=`--accordion-${e}`;return getComputedStyle(this.el).getPropertyValue(i)||t}onClick(e){e.preventDefault(),this.el.style.overflow="hidden",this.el.style.pointerEvents="none",this.el.open?this.close():this.open()}makeContent(){let e=[...this.el.childNodes].filter((e=>!e.isSameNode(this.summary))),t=document.createElement("div");return t.classList.add("accordion__content"),e.forEach((e=>t.appendChild(e))),this.el.appendChild(t),t}open(){this.el.style.height=`${this.el.offsetHeight}px`,this.el.open=!0,window.requestAnimationFrame((()=>this.expand()))}close(){window.requestAnimationFrame((()=>this.shrink()))}expand(){this.state.push(1),this.startHeight=`${this.el.offsetHeight}px`,this.endHeight=`${this.summary.offsetHeight+this.content.offsetHeight}px`,this.el.setAttribute("aria-expanded","true"),this.animate()}shrink(){this.state.push(-1),this.startHeight=`${this.el.offsetHeight}px`,this.endHeight=`${this.summary.offsetHeight}px`,this.el.setAttribute("aria-expanded","false"),this.animate()}animate(){this.animation&&this.animation.cancel(),this.animation=this.el.animate({height:[this.startHeight,this.endHeight]},{duration:this.duration,easing:this.easing}),this.animation.onfinish=()=>this.onAnimationFinish(),this.animation.oncancel=()=>this.state.slice(0,1)}onAnimationFinish(){this.el.open=this.state[1]>0,this.animation=null,this.state=[0],this.startHeight=this.endHeight=null,this.el.style.height=this.el.style.overflow="",this.el.style.pointerEvents="auto"}convertDuration(e){if(/[\d]s$/g.test(e))return 1e3*parseFloat(e);parseInt(e,10)}}class t{constructor(){if(this.noImg=null,this.parentSelector=".item","loading"in HTMLImageElement.prototype)try{this.images=document.querySelectorAll('img[loading="lazy"]'),this.images.forEach((e=>{let t=e.closest(this.parentSelector);e.complete||(t.classList.add("loading"),e.addEventListener("load",(e=>{t.classList.add("loaded")})),e.addEventListener("error",(i=>{console.log("Image "+e.src+" failed to load."),t.classList.add("loaded"),t.classList.add("error"),e.classList.add("placeholder"),null!==this.noImg&&(e.src=this.noImg)})))}))}catch(e){console.log(`Error: ${e}`)}}}let i,s,n=document.querySelector(".image-slider"),o=document.querySelector("#dm-viewer-container");document.querySelector("body").classList.add("js"),o&&n&&(function(){let e=document.querySelector(".images").querySelector("div");n.querySelectorAll(".item").length>1&&(n.addEventListener("glider-loaded",(t=>{e.classList.add("loaded")})),s=new Glider(n,{scrollLock:!0,dots:".dots",arrows:{prev:".slider-btn-prev",next:".slider-btn-next"},duration:1}))}(),n.querySelectorAll(".img-toolbar__tools a:not([href])").forEach((e=>{let t=e.closest("div[id]"),s=Array.from(t.parentElement.children).indexOf(t);e.addEventListener("click",(t=>{t.preventDefault(),e.classList.contains("img-tool-info")?n.querySelectorAll("details > summary").forEach((e=>e.click())):e.classList.contains("img-tool-zoom")&&i.view(s)}))})),function(){let e,t=document.querySelector(".image-slider"),s=t.querySelectorAll("a[data-img]"),n={};["prev","zoomOut","zoomIn","reset","rotateLeft","rotateRight","next"].forEach((e=>{let t=1;("next"===e||"prev"===e)&&s.length<2&&(t=0),n[e]={},n[e].show=t,n[e].size="large"})),i=new Viewer(t,{url:e=>e.parentNode.getAttribute("data-img"),title:t=>(e=t.alt,e),transition:!0,container:o,ready:function(t){let s=i,n=s.title,o=s.toolbar.querySelector("ul"),l=o.querySelectorAll('li[role="button"]');o.addEventListener("mouseleave",(t=>{n.innerHTML=e})),l.forEach((e=>{let t=window.getComputedStyle(e,"before").content.replaceAll('"',"");e.addEventListener("mouseenter",(e=>{n.classList.add("btn-caption"),n.innerHTML=t})),e.addEventListener("mouseleave",(e=>{n.classList.remove("btn-caption")}))}))},toolbar:n}),s.forEach((e=>{e.classList.add("zoomable"),e.addEventListener("click",(e=>{e.preventDefault()}))}))}()),(new t).noImg=document.body.dataset.dmNoImg,document.querySelectorAll(".hamburger").forEach((e=>{e.addEventListener("click",(t=>{e.classList.toggle("is-active")}))})),document.querySelectorAll("details").forEach((t=>{new e(t)})),document.querySelectorAll(".admin-toggle").forEach((e=>{e.addEventListener("change",(e=>{document.body.classList.toggle("hideAdmin")}))})),function(){let e=/^\s*&nbsp;\s*$/;document.querySelectorAll(".item-description").forEach((t=>{t.querySelectorAll("p").forEach((t=>{let i=t.innerHTML;e.test(i)&&t.parentElement.removeChild(t)}))}))}()})()})();
//# sourceMappingURL=dm.js.map