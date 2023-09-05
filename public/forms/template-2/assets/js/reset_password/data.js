



var mainContainer = document.getElementById("pack1");

console.log(document.location.pathname.split('/')[3]);
var $test = document.location.pathname.split('/');
$pagename = $test.pop().replace('html', 'json');
$pageid = document.location.pathname.split('/')[3];
console.log($pageid);
//document.location.pathname.split('/').pop().replace('html', 'json') ?? Math.random()
fetch("assets/js/" + $pageid + "/data.json")

    .then(res => {

        if (res.ok === true) {

            return res.json()



        }

    })

    .then(data => {
       
        console.log(data)



        // console.log(document.querySelector("#logo").src="/images/logo/"+data.logo)

        console.log(data.favicon)

       

        document.querySelector("#name").textContent = data.data.name

        document.querySelector("#comment").textContent = data.data.comment

        if( document.querySelector('#forgetPassUrl') != null){
        document.querySelector('#forgetPassUrl').href = "https://iheb.local.itwise.pro/private-chat-app/public/" + data.data.name + "/forget_password.html"
        }

        if( document.querySelector('#backToLogin') != null){
            document.querySelector('#backToLogin').href = "https://iheb.local.itwise.pro/private-chat-app/public/" + data.data.name + "/index.html"
            }








        $(function () {
            $("#register-form1").on("submit", function (e) {
          
                e.preventDefault();
                //console.log(document.querySelector('#email').value());
                var formdata = new FormData();
          
          
                formdata.append('account',  data.data.accountId);
                formdata.append('name', $('#register-form1 [name="name"]').val());
                formdata.append('email',  $('#register-form1 [name="email"]').val());
                formdata.append('password',  $('#register-form1 [name="password"]').val());
                formdata.append('origin',  data.data.id);
                
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: 'https://iheb.local.itwise.pro/private-chat-app/public/createcontactaccount',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
                        // $.blockUI({
                        //     message: '<span class="spinner-border text-primary"></span><div class="ms-2 loadingfont" style="font-size: 1.2rem;margin-left: 0.5rem;"> Loading... </div>', css: {
                        //         'z-index': 2000,
                        //         position: 'fixed',
                        //         padding: '3px',
                        //         margin: '0px',
                        //         width: 'auto',
                        //         top: 0,
                        //         left: 0,
                        //         /* margin: 0 auto 0 auto, */
                        //         'text-align': 'center',
                        //         color: 'rgb(255, 255, 255)',
                        //         border: 'none',
                        //         'background-color': '#353a3e',
                        //         cursor: 'wait',
                        //         'border-radius': '5px',
                        //         opacity: 0.5,
                        //         display: 'flex',
                        //         bottom: 0,
                        //         right: 0,
                        //         'justify-content': 'center',
                        //         'align-items': 'center',
                        //     }
                        // });
                        document.querySelector('.spinner').classList.remove('d-none');
                        
                    },
                    
                    success: function (data) {
                        console.log(data);
    
    
                        if (data.success) {
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: 'Your account has been created successfully',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            document.querySelector('#register-form1').reset();
                            window.location.href = 'https://stackoverflow.com/';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Your account has not been created successfully',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
          
                        // window.location.reload();
          
          
                    },
                    complete: function () {
                        //$.unblockUI();
                        document.querySelector('.spinner').classList.add('d-none');
                       // document.querySelector('#nav-login-tab').click();
          
                    },
                })
            })
          

            $("#login-form").on("submit", function (e) {
          
                e.preventDefault();
                //console.log(document.querySelector('#email').value());
                var formdata = new FormData();
          
          
               
                formdata.append('login', $('#login-form [name="login"]').val());
                formdata.append('password',  $('#login-form [name="password"]').val());
                
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: 'https://iheb.local.itwise.pro/private-chat-app/public/auth_profile',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
                        // $.blockUI({
                        //     message: '<span class="spinner-border text-primary"></span><div class="ms-2 loadingfont" style="font-size: 1.2rem;margin-left: 0.5rem;"> Loading... </div>', css: {
                        //         'z-index': 2000,
                        //         position: 'fixed',
                        //         padding: '3px',
                        //         margin: '0px',
                        //         width: 'auto',
                        //         top: 0,
                        //         left: 0,
                        //         /* margin: 0 auto 0 auto, */
                        //         'text-align': 'center',
                        //         color: 'rgb(255, 255, 255)',
                        //         border: 'none',
                        //         'background-color': '#353a3e',
                        //         cursor: 'wait',
                        //         'border-radius': '5px',
                        //         opacity: 0.5,
                        //         display: 'flex',
                        //         bottom: 0,
                        //         right: 0,
                        //         'justify-content': 'center',
                        //         'align-items': 'center',
                        //     }
                        // });
                        document.querySelector('.spinner').classList.remove('d-none');
                        
                    },
                    
                    success: function (data) {
                        console.log(data);
                        
                       
    
                        if (data.success == 'true') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: 'Conected successfully',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            window.location.href = 'https://stackoverflow.com/';
                            
                            
                        } else {
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            //document.querySelector('#register-form1').reset();
                        }
          
                        // window.location.reload();
          
          
                    },
                    // error: function(err)  {
                    //     console.log(err);
                    //     Swal.fire({
                    //         icon: 'error',
                    //         title: 'Error!',
                    //         text: err.error,
                    //         showConfirmButton: false,
                    //         timer: 1500
                    //     });
                        
                    //   },
                    complete: function () {
                        //$.unblockUI();
                        document.querySelector('.spinner').classList.add('d-none');
                       // document.querySelector('#nav-login-tab').click();
          
                    },
                })
            })


            $("#forget-password-form").on("submit", function (e) {
          
                e.preventDefault();
                //console.log(document.querySelector('#email').value());
                var formdata = new FormData();
          
               
                formdata.append('login', $('#forget-password-form [name="login"]').val());
                formdata.append('name', data.data.name);
                formdata.append('template', data.data.template);
               
                
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: 'https://iheb.local.itwise.pro/private-chat-app/public/contact/email',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
                        // $.blockUI({
                        //     message: '<span class="spinner-border text-primary"></span><div class="ms-2 loadingfont" style="font-size: 1.2rem;margin-left: 0.5rem;"> Loading... </div>', css: {
                        //         'z-index': 2000,
                        //         position: 'fixed',
                        //         padding: '3px',
                        //         margin: '0px',
                        //         width: 'auto',
                        //         top: 0,
                        //         left: 0,
                        //         /* margin: 0 auto 0 auto, */
                        //         'text-align': 'center',
                        //         color: 'rgb(255, 255, 255)',
                        //         border: 'none',
                        //         'background-color': '#353a3e',
                        //         cursor: 'wait',
                        //         'border-radius': '5px',
                        //         opacity: 0.5,
                        //         display: 'flex',
                        //         bottom: 0,
                        //         right: 0,
                        //         'justify-content': 'center',
                        //         'align-items': 'center',
                        //     }
                        // });
                        document.querySelector('.spinner').classList.remove('d-none');
                        
                    },
                    
                    success: function (data) {
                        console.log(data);
                        
                       
    
                        if (data.success == 'true') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sended!',
                                text: 'Check your email to reset your password',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            //window.location.href = 'https://stackoverflow.com/';
                            
                            
                        } else {
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'There`s no account associated with this email address',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            //document.querySelector('#register-form1').reset();
                        }
          
                        // window.location.reload();
          
          
                    },
                    // error: function(err)  {
                    //     console.log(err);
                    //     Swal.fire({
                    //         icon: 'error',
                    //         title: 'Error!',
                    //         text: err.error,
                    //         showConfirmButton: false,
                    //         timer: 1500
                    //     });
                        
                    //   },
                    complete: function () {
                        //$.unblockUI();
                        document.querySelector('.spinner').classList.add('d-none');
                       // document.querySelector('#nav-login-tab').click();
          
                    },
                })
            })
          
            $("#reset-password-form").on("submit", function (e) {
          
                e.preventDefault();
                //console.log(document.querySelector('#email').value());
                var formdata = new FormData();
                var url = window.location.href;
              
                var id = url.substring(url.lastIndexOf('/') + 1);
               
                formdata.append('password', $('#reset-password-form [name="password"]').val());
                formdata.append('idContact', id.replace('.html', ' '));
               
                var pagename = data.data.name;
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: 'https://iheb.local.itwise.pro/private-chat-app/public/contact/reset_password',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
                        // $.blockUI({
                        //     message: '<span class="spinner-border text-primary"></span><div class="ms-2 loadingfont" style="font-size: 1.2rem;margin-left: 0.5rem;"> Loading... </div>', css: {
                        //         'z-index': 2000,
                        //         position: 'fixed',
                        //         padding: '3px',
                        //         margin: '0px',
                        //         width: 'auto',
                        //         top: 0,
                        //         left: 0,
                        //         /* margin: 0 auto 0 auto, */
                        //         'text-align': 'center',
                        //         color: 'rgb(255, 255, 255)',
                        //         border: 'none',
                        //         'background-color': '#353a3e',
                        //         cursor: 'wait',
                        //         'border-radius': '5px',
                        //         opacity: 0.5,
                        //         display: 'flex',
                        //         bottom: 0,
                        //         right: 0,
                        //         'justify-content': 'center',
                        //         'align-items': 'center',
                        //     }
                        // });
                        document.querySelector('.spinner').classList.remove('d-none');
                        
                    },
                    
                    success: function (data) {
                        console.log(data);
                        
                       
    
                        if (data.success == 'true') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: 'Updated successfully',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            window.location.href = "https://iheb.local.itwise.pro/private-chat-app/public/" + pagename;
                            
                            
                        } else {
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Error!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            //document.querySelector('#register-form1').reset();
                        }
          
                        // window.location.reload();
          
          
                    },
                    // error: function(err)  {
                    //     console.log(err);
                    //     Swal.fire({
                    //         icon: 'error',
                    //         title: 'Error!',
                    //         text: err.error,
                    //         showConfirmButton: false,
                    //         timer: 1500
                    //     });
                        
                    //   },
                    complete: function () {
                        //$.unblockUI();
                        document.querySelector('.spinner').classList.add('d-none');
                       // document.querySelector('#nav-login-tab').click();
          
                    },
                })
            })



        
        })

       


      

    

      

    })



