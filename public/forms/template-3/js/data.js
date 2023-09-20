
var baseElement = document.querySelector("base");
var baseUrl =""
if (baseElement) {
    var urlhref = baseElement.getAttribute("href");
    var formsIndex = urlhref.indexOf("forms");
    if (formsIndex !== -1) {
         baseUrl = urlhref.substring(0, formsIndex);
            if (!baseUrl.endsWith("/") && !baseUrl.endsWith("\\")) {
                baseUrl += "/";
            } else if (baseUrl.endsWith("\\")) {
                baseUrl = baseUrl.replace(/\\/g, '/');
            }
        console.log("Base URL: " + baseUrl);
    } else {
        console.log("The 'forms' directory was not found in the URL.");
    }
}




var mainContainer = document.getElementById("pack1");
console.log(document.location.pathname.split('/'));
var $test = document.location.pathname.split('/');
$pagename = $test.pop().replace('html', 'json');
$pageid = $test.pop();
console.log($pageid);




var ApiUrl="";
var AssetsUrl="assets/js/" + $pageid + "/data.json";
if(baseUrl.startsWith("C:")){
    AssetsUrl="https://iheb.local.itwise.pro/private-chat-app/public/forms/template-1/assets/js/test6/data.json";
    ApiUrl="http://127.0.0.1:8000/"
 }else{
    AssetsUrl="assets/js/" + $pageid + "/data.json";
    ApiUrl=baseUrl
 }


fetch(AssetsUrl)
    .then(res => {
        if (res.ok === true) {
            return res.json()
        }
    })
    .then(data => {
       
        console.log(data)



        if(document.querySelector('#forgetPassUrl1') != null){
            document.querySelector('#forgetPassUrl1').href = baseUrl + data.data.slug_url + "/forget_password.html"
        }
        if(document.querySelector('#backToLogin') != null){
            document.querySelector('#backToLogin').href = baseUrl + data.data.slug_url + "/index.html"
        }
        document.querySelector("#name").textContent = data.data.name
        document.querySelector("#comment").textContent = data.data.comment

        



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
                document.querySelector('#signup1')?.classList.add('disabled');


                   $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ApiUrl+'createcontactaccount',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    beforeSend: function (xhr) {
                        //xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
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
                    
                    success: function (responcreat) {
                        console.log(responcreat);
    
    
                        if (responcreat.success) {
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: 'Your account has been created successfully',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // window.location.href = 'https://stackoverflow.com/';
                           // document.querySelector('#register-form1').reset();
                           let redirectUrl = data.data.redirect_url;
                           // Check if the URL doesn't start with "http://" or "https://"
                           if (!/^https?:\/\//i.test(redirectUrl)) {
                             redirectUrl = "http://" + redirectUrl;
                           }
                           window.location.href = redirectUrl;
                        
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
                        //document.querySelector('#home-tab').click();
                        document.querySelector('#signup1')?.classList.remove('disabled');

                    },
                })
            })
            function ValidateEmail(email) {
                var name = document.getElementById('email2').value;

                var ctrl = $("#email2");
                var error = $("#errormessagename");
                
                 if (name.includes('@')) {
                    var formdata = new FormData();
          
          
                    formdata.append('account',  data.data.accountId);
                    formdata.append('login',  $('#register-form1 [name="email"]').val());
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: ApiUrl+'check_email_contact',
                        processData: false,
                        contentType: false,
                        data: formdata,
                        beforeSend: function (xhr) {
                            //xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
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
                           // document.querySelector('.spinner').classList.remove('d-none');
                            
                        },
                       
                        success: function(response) {
                            var ctrl = $("#email2");
                            var error = $("#errormessagename");

                            console.log(response.success);
                            // document.querySelector("#signup1").classList.add('disabled');
                           
                            if (response.data.length == 0 ) {
                                //document.querySelector("#email2").classList.remove('is-invalid');
                                //document.querySelector("#errormessagename").classList.remove('d-none');
                                 ctrl.addClass("is-valid").removeClass("is-invalid");
                                 error.addClass("d-none");
                                document.querySelector("#signup1").classList.remove('disabled');
                            } else {
                               // document.querySelector("#email2").classList.add('is-invalid');
                                //document.querySelector("#errormessagename").classList.add('d-none');
                               ctrl.addClass("is-invalid").removeClass("is-valid");
                                 error.removeClass('d-none');
                                document.querySelector("#signup1").classList.add('disabled');
                            }

                        },
                        /*complete: function() {
                            $.unblockUI();

                        },*/


                    })
                }
            }



            $("document").ready(function () {

                $("#email2").bind("keyup", function() {
                    ValidateEmail($(this).val());
                });

                $('#passwordRegister, #email2 , #name2, #passwordRegisterConfirm').on('change input', function () {
                    //btn.classList.add('disabled');
                    const input = document.querySelector('#passwordRegister');
                    const input1 = document.querySelector('#email2');
                    const input2 = document.querySelector('#name2');
                    const input3 = document.querySelector('#passwordRegisterConfirm');
                    // if(_this.form.value.role != null){
                    //   document.querySelector('#display-data2')?.classList.remove('disabled');
                    // }else{
                    //   document.querySelector('#display-data2')?.classList.add('disabled');
              
                    // }
                    // const input2 = document.querySelector('#addrole') as HTMLInputElement;
                    // const input3 = document.querySelector('#addfile') as  HTMLInputElement ;
                    //  const input4 = document.querySelector('#addpresentation') as  HTMLInputElement ;
                    //  const input5 = document.querySelector('#basic-default-radio-male1') as  HTMLInputElement;
                    //  const input6 = document.querySelector('#basic-default-radio-female1') as  HTMLInputElement;
              
                    if (input.value == '' || input3.value == '' || input1.value == '' || input2.value == ''  || input1.classList.contains('is-invalid')) {
                      document.querySelector('#signup1')?.classList.add('disabled');
                    } else {
                      document.querySelector('#signup1')?.classList.remove('disabled');
                    }
                  });
            });


            $("#login-form").on("submit", function (e) {
          
                e.preventDefault();
                //console.log(document.querySelector('#email').value());
                var formdata = new FormData();
          
          
                formdata.append('account_id',  data.data.accountId);
                formdata.append('login', $('#login-form [name="login"]').val());
                formdata.append('password',  $('#login-form [name="password"]').val());
                document.querySelector('#signin1')?.classList.add('disabled');

           
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ApiUrl+'auth_profile',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    beforeSend: function (xhr) {
                       // xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
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
                    
                    success: function (responseathu) {
                        console.log(responseathu);
                        
                       
    
                        if (responseathu.success == 'true') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: 'Conected successfully',
                                showConfirmButton: false,
                                timer: 1500
                            });
                         
                            let redirectUrl = data.data.redirect_url;
                            // Check if the URL doesn't start with "http://" or "https://"
                            if (!/^https?:\/\//i.test(redirectUrl)) {
                              redirectUrl = "http://" + redirectUrl;
                            }
                            window.location.href = redirectUrl;
                            
                            
                            
                        } else {
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: responseathu.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            //document.querySelector('#register-form1').reset();
                        }
          
                        // window.location.reload();
          
          
                    },
                    complete: function () {
                        //$.unblockUI();
                        document.querySelector('.spinner').classList.add('d-none');
                       // document.querySelector('#home-tab').click();
                       document.querySelector('#signin1')?.classList.remove('disabled');

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
               
                document.querySelector('#sendsetlink1')?.classList.add('disabled');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ApiUrl+'contact/email',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    beforeSend: function (xhr) {
                        //xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
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
                       document.querySelector('#sendsetlink1')?.classList.remove('disabled');

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
                formdata.append('idContact', id);
                document.querySelector('#resetpwd1')?.classList.add('disabled');

                var pagename = data.data.name;
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ApiUrl+'contact/reset_password',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    beforeSend: function (xhr) {
                        //xhr.setRequestHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg5NjUzMzQsImV4cCI6MTYyMDA1NzIzMzMzLCJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJ0ZXN0QGdtYWlsLmNvbSJ9.fUm3v7Bk6ooi0J8LJ9WmAmsIYsJUZlfvNplrnPgPnP0j3k2lf4E9GsltoqeQin20pnUoMQq7O5CQKjuqK8xO8WAeORC1yMX0dhdlXZapd9SQKCFrEviS_JoXiLOyB7qeNiaKlzm4n-gpDX0o6_LuN__p6u4_WB_abHI3dOmsJwliU4SElXQhfqYPDnkT9dcnHIHt6fv9H0urApxF42oSMMvhXYT_UJeL6r9cJ-tzHdqtpl6tsfsWhPgz1WdjuRyTZI-xctDIpDoX3xZ8wwruXMjEAPMfbz6UbX6FYJbBnNYrETsdS1lXgrWhnAmLVJT_6TzHfOmeGJZP-fDDnr7ozg');
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
                            window.location.href = baseUrl + pagename;                            
                            
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
                       document.querySelector('#resetpwd1')?.classList.remove('disabled');

                    },
                })
            })


          
          })



       


      

    

      

    })



