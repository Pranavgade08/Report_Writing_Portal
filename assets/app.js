function qs(sel, root=document){return root.querySelector(sel)}
function qsa(sel, root=document){return Array.from(root.querySelectorAll(sel))}

function wireConfirmDelete(){
  qsa('[data-confirm]').forEach(el=>{
    el.addEventListener('click', (e)=>{
      const msg = el.getAttribute('data-confirm') || 'Are you sure?';
      if(!confirm(msg)) e.preventDefault();
    })
  })
}

document.addEventListener('DOMContentLoaded', ()=>{
  wireConfirmDelete();
});
