import Swal from 'sweetalert2';

$(document).ready(() => {
   $('.delete-button-alert').click(function(e)  {
      e.preventDefault();
       var form = $(this).parents('form');
       swAlert(form, "You won't be able to revert this!")
   });

    $('.action-button-alert').click(function(e)  {
        e.preventDefault();
        var form = $(this).parents('form');
        swAlert(form, "")
    });

    function swAlert(form, text) {
        Swal.fire({
            title: 'Are you sure?',
            text: text,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                form.submit();
            }
        })
    }
});
