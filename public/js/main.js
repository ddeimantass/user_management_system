// const users = document.getElementById('users');
//
// if (users) {
//     users.addEventListener('click', e => {
//         if (e.target.className === 'btn btn-danger delete-user') {
//             if (confirm('Are you sure?')) {
//                 const id = e.target.getAttribute('data-id');
//
//                 fetch(`/admin/user/delete/${id}`, {
//                     method: 'DELETE'
//                 }).then(function(response) {
//                     if (response.status === 200) {
//                         window.location.reload()
//                     }
//                     else{
//                         alert("Failed to delete user!");
//                     }
//                 })
//             }
//         }
//     });
// }
//
// const groups = document.getElementById('groups');
//
// if (groups) {
//     groups.addEventListener('click', e => {
//         if (e.target.className === 'btn btn-danger delete-group') {
//             if (confirm('Are you sure?')) {
//                 const id = e.target.getAttribute('data-id');
//
//                 fetch(`/admin/group/delete/${id}`, {
//                     method: 'DELETE'
//                 }).then(function(response) {
//                         if (response.status === 200) {
//                             window.location.reload()
//                         }
//                         else{
//                             alert("Failed to delete group with users!");
//                         }
//                     })
//             }
//         }
//     });
// }