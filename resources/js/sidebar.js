let btn = document.querySelector("#btn");
let sidebar = document.querySelector(".sidebar");
let dropdown = document.querySelectorAll(".dropdown-btn");

btn.onclick = function() {
    sidebar.classList.toggle("active");
};

dropdown.forEach(button => {
    button.addEventListener("click", function() {
        this.classList.toggle("active");
        let dropdownContent = this.querySelector(".dropdown-container");
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    });
});