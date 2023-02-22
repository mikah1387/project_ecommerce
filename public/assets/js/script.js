


  let btndelete = document.querySelectorAll('[data-bs-toggle]')
 
 
    btndelete.forEach(btn => {

      btn.addEventListener('click',function(){
        document.querySelector('.confdelete').href='/admin/categories/delete/'+this.dataset.id
        
         document.querySelector('.modal-body p').innerText = 'voulez-vous supprimer la categorie  '+ this.dataset.title;
      })
    
   });

   let btndeleteproduct = document.querySelectorAll('.delete_product')
 
   console.log(btndeleteproduct)
 
   btndeleteproduct.forEach(btn => {
      
     btn.addEventListener('click',function(){
       document.querySelector('.confdelete').href='/admin/products/delate/'+this.dataset.id
       
        document.querySelector('.modal-body p').innerText = 'voulez-vous supprimer le produit '+ this.dataset.title;
     })
   
  });
  var swiper = new Swiper(".mySwiper", {
    pagination: {
      el: ".swiper-pagination",
      type: "progressbar",
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });