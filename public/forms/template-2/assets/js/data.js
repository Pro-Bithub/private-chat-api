//var baseElement = document.querySelector("base");
var baseUrl = "";

var currentUrl = window.location.href;
var lastSlashIndex = currentUrl.lastIndexOf("/");
var currentUrlresult = currentUrl.substring(0, lastSlashIndex + 1);

if (apiurl) {
  baseUrl = apiurl;
  if (!apiurl.endsWith("/") && !apiurl.endsWith("\\")) {
    baseUrl += "/";
  } else if (apiurl.endsWith("\\")) {
    baseUrl = apiurl.replace(/\\/g, "/");
  }
}

var mainContainer = document.getElementById("pack1");
//console.log(document.location.pathname.split('/'));
var $test = document.location.pathname.split("/");
$pagename = $test.pop().replace("html", "json");
$pageid = $test.pop();
//console.log($pageid);

var ApiUrl = "";

var AssetsUrl = "data.json";


var lang = "en";
var defaultmessegelogin_faild =[ 'Email not exist','Password not valid','User not found'];


if (baseUrl.includes(":8000")) {
  AssetsUrl =
    "https://hatem.workshop.itwise.pro/private_chat/api/public/forms/template-1/assets/js/data_example.json";
  ApiUrl = "http://127.0.0.1:8000/";
} else {
  AssetsUrl = "data.json";
  ApiUrl = baseUrl;
}

fetch(AssetsUrl)
  .then((res) => {
    if (res.ok === true) {
      return res.json();
    }
  })

  .then((data) => {
    console.log(data);

    if( data.data.hasOwnProperty('lang') )
    if( data.data.lang==2 )
    lang = "fr";

    if (document.querySelector("#forgetPassUrl1") != null) {
        document.querySelector("#forgetPassUrl1").href =
          currentUrlresult + "forget_password.html";
      }
      if (document.querySelector("#backToLogin") != null) {
        document.querySelector("#backToLogin").href =
          currentUrlresult  + "index.html";
      }
  
    document.querySelector("#name").textContent = data.data.name;

    document.querySelector("#comment").textContent = data.data.comment;

    $(function () {
  $("#register-form1").on("submit", function (e) {

        
        e.preventDefault();

        const input = document.querySelector("#passwordRegister");
        const input1 = document.querySelector("#email2");
        const input2 = document.querySelector("#name2");
     
      
        if (
          input.value == "" ||
          input1.value == "" ||
          input2.value == "" ||
          input1.classList.contains("is-invalid")
        ) {
        
          Swal.fire({
            icon: "warning",
            title: "Oops!",
            text: demoJson.demo.popupAlert.allRequiredFields[lang],
            showConfirmButton: false,
            timer: 1500,
        });
          
        }  else {
      
        var formdata = new FormData();
        formdata.append("account", data.data.accountId);
        formdata.append("name", $('#register-form1 [name="name"]').val());
        formdata.append("email", $('#register-form1 [name="email"]').val());

        formdata.append("applicationName", data.data.name);
        

        formdata.append(
          "password",
          $('#register-form1 [name="password"]').val()
        );
        formdata.append("origin", data.data.id);
        formdata.append("lang",lang);

        document.querySelector("#signup1")?.classList.add("disabled");

        $.ajax({
          type: "POST",
          dataType: "json",
          url: ApiUrl + "createcontactaccount",
          processData: false,
          contentType: false,
          data: formdata,
          beforeSend: function (xhr) {
            document.querySelector(".spinner").classList.remove("d-none");
          },

          success: function (responcreat) {
            console.log(responcreat);

            if (responcreat.success) {
              Swal.fire({
                icon: "success",
                title:
                  demoJson.demo.popupAlert.accountCreatedSucceAlert[lang],
                text: demoJson.demo.popupAlert.accountCreatedSucce[lang],
                showConfirmButton: true, // Show the "OK" button
                timer: 1500,
                didClose: () => {
                  // User clicked "OK," so perform the redirection
                  let redirectUrl = data.data.redirect_url;
                  // Check if the URL doesn't start with "http://" or "https://"
                  if (!/^https?:\/\//i.test(redirectUrl)) {
                    redirectUrl = "http://" + redirectUrl;
                  }
                        const email =  encodeURIComponent($('#register-form1 [name="email"]').val())

                      const Url = `/verification_step.html?email=${email}&account=${data.data.accountId}`;
                      if (data.data.url.slice(-1) !== '/') {
                    // If not, add a forward slash at the end
                    data.data.url += '/';
                }

                  window.location.href = data.data.url + data.data.slug_url + Url;
                  $("#register-form1")[0].reset();
                },
              });
              /*   .then((result) => {
              if (result.isConfirmed) {
                // User clicked "OK," so perform the redirection
                window.location.href =
                  baseUrl + data.data.slug_url + "/index.html";
              }
            }); */
            } else {
              Swal.fire({
                icon: "error",
                text: demoJson.demo.popupAlert[
                  responseathu.error_type.toLowerCase()
                ][lang],
                showConfirmButton: false,
                timer: 1500,
              });
            }

            // window.location.reload();
          },
          complete: function () {
            //$.unblockUI();
            document.querySelector(".spinner").classList.add("d-none");
            //document.querySelector('#login-tab').click();
            document.querySelector("#signup1")?.classList.remove("disabled");
          },
        });

        } 


      });


      function ValidateEmail(email) {
        var name = document.getElementById("email2").value;

        var ctrl = $("#email2");
        var error = $("#errormessagename");
        if (name.length > 0) {
          document.querySelector("#labelemail1").classList.add("d-none");
        } else {
          document.querySelector("#labelemail1").classList.remove("d-none");
        }
        if (name.includes("@")) {
          var formdata = new FormData();

          formdata.append("account", data.data.accountId);
          formdata.append("login", $('#register-form1 [name="email"]').val());
          $.ajax({
            type: "POST",
            dataType: "json",
            url: ApiUrl + "check_email_contact",
            processData: false,
            contentType: false,
            data: formdata,
            beforeSend: function (xhr) {},

            success: function (response) {
              var ctrl = $("#email2");
              var error = $("#errormessagename");

              console.log(response.success);
              

              if (response.data.length == 0) {
                //document.querySelector("#email2").classList.remove('is-invalid');
                //document.querySelector("#errormessagename").classList.remove('d-none');
                ctrl.addClass("is-valid").removeClass("is-invalid");
                error.addClass("d-none");
                document.querySelector("#signup1").classList.remove("disabled");
              } else {
                // document.querySelector("#email2").classList.add('is-invalid');
                //document.querySelector("#errormessagename").classList.add('d-none');
                ctrl.addClass("is-invalid").removeClass("is-valid");
                error.removeClass("d-none");
                document.querySelector("#signup1").classList.add("disabled");
              }
            },
            /*complete: function() {
                            $.unblockUI();

                        },*/
          });
        }
      }

      $("document").ready(function () {
        $("#email2").bind("keyup", function () {
          ValidateEmail($(this).val());
        });
/* 
        $("#passwordRegister, #email2 , #name2").on(
          "change input",
          function () {
            //btn.classList.add('disabled');
            const input = document.querySelector("#passwordRegister");
            const input1 = document.querySelector("#email2");
            const input2 = document.querySelector("#name2");

            if (
              input.value == "" ||
              input1.value == "" ||
              input2.value == "" ||
              input1.classList.contains("is-invalid")
            ) {
              document.querySelector("#signup1")?.classList.add("disabled");
            } else {
              document.querySelector("#signup1")?.classList.remove("disabled");
            }
          }
        ); */
      });

      $("#login-form").on("submit", function (e) {
        e.preventDefault();
        //console.log(document.querySelector('#email').value());
        var formdata = new FormData();

        formdata.append("account_id", data.data.accountId);
        formdata.append("login", $('#login-form [name="login"]').val());
        formdata.append("password", $('#login-form [name="password"]').val());
        document.querySelector("#signin1")?.classList.add("disabled");
        $.ajax({
          type: "POST",
          dataType: "json",
          url: ApiUrl + "auth_profile",
          processData: false,
          contentType: false,
          data: formdata,
          beforeSend: function (xhr) {
            document.querySelector(".spinner").classList.remove("d-none");
          },
          success: function (responseathu) {
            console.log(responseathu);
            console.log(responseathu.success);

            if (responseathu.success == "true") {
              Swal.fire({
                icon: "success",
                title:demoJson.demo.popupAlert.loginSuccessfullytitle[lang]   ,
                text: demoJson.demo.popupAlert.loginSuccessfullytext[lang]  ,
                showConfirmButton: false,
                timer: 2500,
                didClose: () => {
                  // User clicked "OK," so perform the redirection
                  let redirectUrl = data.data.redirect_url;
                  // Check if the URL doesn't start with "http://" or "https://"
                  if (!/^https?:\/\//i.test(redirectUrl)) {
                    redirectUrl = "http://" + redirectUrl;
                  }
                  const id =
                    responseathu.data.username != null
                      ? responseathu.data.id
                      : null;
                  const accountId =
                    responseathu.data.username != null
                      ? responseathu.data.accountId
                      : null;
                  const username =
                    responseathu.data.username != null
                      ? encodeURIComponent(responseathu.data.username)
                      : null;
                  const login =
                    responseathu.data.login != null
                      ? encodeURIComponent(responseathu.data.login)
                      : null;

                  const updatedUrl = `${redirectUrl}?id=${id}&accountId=${accountId}&username=${username}&login=${login}&action=login`;

                  window.location.href = updatedUrl;
                },
              });
            } else {
              var indexOfMatch = defaultmessegelogin_faild.findIndex(function (defaultMessage) {
                return responseathu.message.includes(defaultMessage);
            });
            var message ="Password not valid"
            if (indexOfMatch !== -1) {
              const index = String(defaultmessegelogin_faild[indexOfMatch]).toLowerCase().replace(/\s+/g, '');
              console.log(index)
              console.log(indexOfMatch)
              message =  demoJson.demo.popupAlert[index][lang]
              }

              Swal.fire({
                icon: "error",
                title: demoJson.demo.popupAlert.errorAlert[lang] ,
                text: message,
                showConfirmButton: false,
                timer: 1500,
              });
              //document.querySelector('#register-form1').reset();
            }

            // window.location.reload();
          },

          complete: function () {
            //$.unblockUI();
            document.querySelector(".spinner").classList.add("d-none");
            // document.querySelector('#nav-login-tab').click();
            document.querySelector("#signin1")?.classList.remove("disabled");
          },
        });
      });

      $("#forget-password-form").on("submit", function (e) {
        e.preventDefault();
        //console.log(document.querySelector('#email').value());
        var formdata = new FormData();

        formdata.append(
          "login",
          $('#forget-password-form [name="login"]').val()
        );
        formdata.append("name", data.data.name);
        formdata.append("template", data.data.template);
        formdata.append("url", currentUrlresult);

        document.querySelector("#sendsetlink1")?.classList.add("disabled");
        $.ajax({
          type: "POST",
          dataType: "json",
          url: ApiUrl + "contact/email",
          processData: false,
          contentType: false,
          data: formdata,
          beforeSend: function (xhr) {
            document.querySelector(".spinner").classList.remove("d-none");
          },

          success: function (data) {
            console.log(data);

            if (data.success == "true") {

                
              Swal.fire({
                icon: "success",
                title: "Sended!",
                text:   demoJson.demo.popupAlert.checkEmail[lang] ,
                showConfirmButton: false,
                timer: 1500,
              });

              $("#forget-password-form")[0].reset();
            } else {
              Swal.fire({
                icon: "error",
                title: "Error!",
                text:  demoJson.demo.popupAlert.noaccount[lang],
                showConfirmButton: false,
                timer: 1500,
              });
              //document.querySelector('#register-form1').reset();
            }

            // window.location.reload();
          },

          complete: function () {
            //$.unblockUI();
            document.querySelector(".spinner").classList.add("d-none");
            // document.querySelector('#nav-login-tab').click();
            document
              .querySelector("#sendsetlink1")
              ?.classList.remove("disabled");
          },
        });
      });

      function getParameterUidFromURL() {
        var urlParams = new URLSearchParams(window.location.search);
        var uid = urlParams.get("uid");
        if (!uid || uid.trim() === "") {
          return null;
        }
        return uid;
      }

      $("#reset-password-form").on("submit", function (e) {
        e.preventDefault();

        var uid = getParameterUidFromURL();

        if (uid) {
          //console.log(document.querySelector('#email').value());
          var formdata = new FormData();

          formdata.append(
            "password",
            $('#reset-password-form [name="password"]').val()
          );
          formdata.append("uid", uid);
          document.querySelector("#resetpwd1")?.classList.add("disabled");

          var pagename = data.data.name;

          $.ajax({
            type: "POST",
            dataType: "json",
            url: ApiUrl + "contact/reset_password",
            processData: false,
            contentType: false,
            data: formdata,
            beforeSend: function (xhr) {
              document.querySelector(".spinner").classList.remove("d-none");
            },

            success: function (data) {
              console.log(data);

              if (data.success == "true") {

                Swal.fire({
                    icon: "success",
                    title:demoJson.demo.popupAlert.resetSuccessful[lang] ,
                    text: demoJson.demo.popupAlert.updateresetSuccessful[lang],
                    showConfirmButton: false,
                    timer: 2500,
                    didClose: () => {
                        $("#reset-password-form")[0].reset();
                        window.location.href = currentUrlresult;
                    },
                  });

          
            
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error!",
                  text: "Error!",
                  showConfirmButton: false,
                  timer: 1500,
                });
                //document.querySelector('#register-form1').reset();
              }

              // window.location.reload();
            },

            complete: function () {
              //$.unblockUI();
              document.querySelector(".spinner").classList.add("d-none");
              // document.querySelector('#nav-login-tab').click();
              document
                .querySelector("#resetpwd1")
                ?.classList.remove("disabled");
            },
          });
        } else {
          Swal.fire({
            text: "Invalid URL. Please provide a valid UID.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: { confirmButton: "btn btn-light" },
          }).then(function () {
            $("#reset-password-form")[0].reset();
          });
        }
      });

      $("#kt_sing_in_two_steps_form").on("submit", function (e) {
        e.preventDefault();
        const email = $('#kt_sing_in_two_steps_form [name="email"]').val();

        if (email == "") {
          Swal.fire({
            text: "Invalid URL. Please provide a valid email.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: { confirmButton: "btn btn-light" },
          }).then(function () {
            $("#kt_sing_in_two_steps_form")[0].reset();
          });
        } else {
          var form = document.querySelector("#kt_sing_in_two_steps_form");

          var isFormValid = true;
          var inputs = [].slice.call(
            form.querySelectorAll('input[maxlength="1"]')
          );
          var values = "";
          // Check if all input fields have a value
          inputs.map(function (input) {
            if (input.value === "" || input.value.length === 0) {
              isFormValid = false;
            } else {
              values += input.value;
            }
          });
          if (isFormValid) {
            var formdata = new FormData();

            formdata.append("account", data.data.accountId);
            formdata.append("receiver", email);
            formdata.append("code", values);
            document
              .querySelector("#kt_sing_in_two_steps_submit")
              ?.classList.add("disabled");
            document.querySelector("#resendLink")?.classList.add("disabled");

            $.ajax({
              type: "POST",
              dataType: "json",
              url: ApiUrl + "login/2fa/verify",
              processData: false,
              contentType: false,
              data: formdata,
              beforeSend: function (xhr) {
                $(".indicator-progress").css("display", "block");
                $(".indicator-label").css("display", "none");
                document.querySelector(".spinner").classList.remove("d-none");
              },

              success: function (responseathu) {
                $(".indicator-progress").css("display", "none");
                $(".indicator-label").css("display", "block");
                console.log(responseathu);

                if (responseathu.success == true) {
                  Swal.fire({
                    icon: "success",
                    text: demoJson.demo.popupAlert.accountverified[lang],
                    showConfirmButton: false,
                    timer: 2500,
                    didClose: () => {

                       // User clicked "OK," so perform the redirection
                  let redirectUrl = data.data.redirect_url;
                  // Check if the URL doesn't start with "http://" or "https://"
                  if (!/^https?:\/\//i.test(redirectUrl)) {
                    redirectUrl = "http://" + redirectUrl;
                  }
                  const id =
                    responseathu.data.username != null
                      ? responseathu.data.id
                      : null;
                  const accountId =
                    responseathu.data.username != null
                      ? responseathu.data.accountId
                      : null;
                  const username =
                    responseathu.data.username != null
                      ? encodeURIComponent(responseathu.data.username)
                      : null;
                  const login =
                    responseathu.data.login != null
                      ? encodeURIComponent(responseathu.data.login)
                      : null;

                  const updatedUrl = `${redirectUrl}?id=${id}&accountId=${accountId}&username=${username}&login=${login}&action=login`;

                  window.location.href = updatedUrl;


      
                    },
                  });
                } else {
                  Swal.fire({
                    icon: "error",
                    title: demoJson.demo.popupAlert.errorAlert[lang],
                    text: demoJson.demo.popupAlert[
                      responseathu.error_type.toLowerCase()
                    ][lang],
                    showConfirmButton: false,
                    timer: 1500,
                  });
                  if( responseathu.error_type.toLowerCase()=='noaccount'){
                    const Url = `/index.html`;
                    if (data.data.url.slice(-1) !== '/') {
                      // If not, add a forward slash at the end
                      data.data.url += '/';
                  }
                  
                    window.location.href =  data.data.url  + data.data.slug_url + Url;
                  }
                  //document.querySelector('#register-form1').reset();
                }

                // window.location.reload();
              },

              complete: function () {
                $(".indicator-progress").css("display", "none");
                $(".indicator-label").css("display", "block");
                document
                  .querySelector("#kt_sing_in_two_steps_submit")
                  ?.classList.remove("disabled");
                document
                  .querySelector("#resendLink")
                  ?.classList.remove("disabled");
              },
            });
          } else {
            Swal.fire({
              text: demoJson.demo.fillverificationcode[lang],
              icon: "error",
              buttonsStyling: false,
              confirmButtonText: demoJson.demo.gotit[lang],
              customClass: { confirmButton: "btn btn-light" },
            }).then(function () {});
          }
        }
      });
      
      $("#resendLink").click(function (e) {
        e.preventDefault();

        // Get the email value from the input field
        var email = document.querySelector('input[name="email"]').value;
        // Disable the button
        $("#resendLink").prop("disabled", true);

        var formdata = new FormData();
        formdata.append("account", data.data.accountId);
        formdata.append("receiver", email);
        $.ajax({
          type: "POST",
          dataType: "json",
          url: ApiUrl + "login/2fa/generate",
          processData: false,
          contentType: false,
          data: formdata,
          success: function (data) {
            $("#resendLink").prop("disabled", false);

            if (data.success == true) {
              Swal.fire({
                icon: "success",
                text: demoJson.demo.popupAlert.codesentmessage[lang],
                showConfirmButton: false,
                timer: 2500,
                didClose: () => {
                  // Deactivate the button after 15 seconds
                  var countdown = 120;
                  var timerElement = $("#timer");
                  var timerInterval = setInterval(function () {
                    countdown--;
                    if (countdown >= 0) {
                      // timerElement.text('Time remaining: ' + countdown + ' seconds');
                    } else {
                      clearInterval(timerInterval);
                      timerElement.text("");
                      $("#resendLink").prop("disabled", false);
                    }
                  }, 1000);
                },
              });
            } else {
              Swal.fire({
                icon: "error",
                title: demoJson.demo.popupAlert.errorAlert[lang],
                text: demoJson.demo.popupAlert[data.error_type.toLowerCase()][
                  lang
                ],
                showConfirmButton: false,
                timer: 1500,
              });
              //document.querySelector('#register-form1').reset();
            }

            // window.location.reload();
          },

          complete: function () {
            $("#resendLink").prop("disabled", false);
          },
        });
      });

    });
  });


  var form = document.querySelector("#kt_sing_in_two_steps_form");

// Focus and keyup events for input fields for verification_step
var inputs = [
  form.querySelector("[name=code_1]"),
  form.querySelector("[name=code_2]"),
  form.querySelector("[name=code_3]"),
  form.querySelector("[name=code_4]"),
  form.querySelector("[name=code_5]"),
  form.querySelector("[name=code_6]"),
];

inputs[0].focus();

inputs.map(function (input, index) {
  input.addEventListener("keyup", function () {
    console.log("hi");
    if (this.value.length === 1 && index < inputs.length - 1) {
      inputs[index + 1].focus();
    }
  });
});

inputs[inputs.length - 1].addEventListener("keyup", function () {
  if (this.value.length === 1) {
    this.blur();
  }
});

