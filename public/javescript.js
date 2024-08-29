const deleteButtons = document.querySelectorAll('.deleteA')

deleteButtons.forEach((deleteButton) => {


    deleteButton.addEventListener('click',()=> {


        const popup = deleteButton.nextElementSibling

        popup.style.display = "block";

        const noDelete = popup.nextElementSibling
        noDelete.style.display = "block";


        deleteButton.style.display = "none";


        noDelete.addEventListener('click',()=> {
            noDelete.style.display = "none";
            popup.style.display = "none";
            deleteButton.style.display = "block";

    });
    });
})