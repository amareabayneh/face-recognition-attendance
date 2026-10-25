// Scroll Top Button

let btn = document.getElementById("scrollTop");

window.onscroll = function(){

if(document.documentElement.scrollTop > 200){
btn.style.display="block";
}else{
btn.style.display="none";
}

}

btn.onclick = function(){
window.scrollTo({
top:0,
behavior:"smooth"
});
}



const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('sidebarToggle');

toggleBtn.addEventListener('click', () => {
    // For small screens
    if(window.innerWidth <= 768){
        sidebar.classList.toggle('open');
    } else {
        sidebar.classList.toggle('closed');
    }

    // Toggle icon
    if(sidebar.classList.contains('closed') || sidebar.classList.contains('open') && window.innerWidth <= 768){
        toggleBtn.classList.remove('bi-filter-right');
        toggleBtn.classList.add('bi-x'); // close icon
    } else {
        toggleBtn.classList.remove('bi-x');
        toggleBtn.classList.add('bi-filter-right'); // menu icon
    }
});

// Optional: close sidebar on click outside on mobile
document.addEventListener('click', function(e){
    if(window.innerWidth <= 768){
        if(!sidebar.contains(e.target) && !toggleBtn.contains(e.target)){
            sidebar.classList.remove('open');
            toggleBtn.classList.remove('bi-x');
            toggleBtn.classList.add('bi-filter-right');
        }
    }
});
