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
console.log(document.location.pathname.split("/"));
var $test = document.location.pathname.split("/");
$pagename = $test.pop().replace("html", "json");
$pageid = $test.pop();
console.log($pageid);

var ApiUrl = "";
var AssetsUrl = "data.json";
if (baseUrl.startsWith("C:")) {
  AssetsUrl =
    "https://iheb.local.itwise.pro/private-chat-app/public/forms/template-1/assets/js/test6/data.json";
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

    if (document.querySelector("#forgetPassUrl1") != null) {
      document.querySelector("#forgetPassUrl1").href =
        baseUrl + data.data.slug_url + "/forget_password.html";
    }
    if (document.querySelector("#backToLogin") != null) {
      document.querySelector("#backToLogin").href =
        baseUrl + data.data.slug_url + "/index.html";
    }
    document.querySelector("#name").textContent = data.data.name;
    document.querySelector("#comment").textContent = data.data.comment;

    $(function () {
      $("#register-form1").on("submit", function (e) {
        e.preventDefault();
        //console.log(document.querySelector('#email').value());
        var formdata = new FormData();

        formdata.append("account", data.data.accountId);
        formdata.append("name", $('#register-form1 [name="name"]').val());
        formdata.append("email", $('#register-form1 [name="email"]').val());
        formdata.append(
          "password",
          $('#register-form1 [name="password"]').val()
        );
        formdata.append("origin", data.data.id);
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
                title: "Added!",
                text: "Your account has been created successfully",
                showConfirmButton: true, // Show the "OK" button
                timer: 1500,
              }).then((result) => {
                if (result.isConfirmed) {
                  // User clicked "OK," so perform the redirection
                  window.location.href =
                    baseUrl + data.data.slug_url + "/index.html";
                }
              });

              $("#register-form1")[0].reset();
            } else {
              Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Your account has not been created successfully",
                showConfirmButton: false,
                timer: 1500,
              });
            }

            // window.location.reload();
          },
          complete: function () {
            //$.unblockUI();
            document.querySelector(".spinner").classList.add("d-none");
            //document.querySelector('#home-tab').click();
            document.querySelector("#signup1")?.classList.remove("disabled");
          },
        });
      });
      function ValidateEmail(email) {
        var name = document.getElementById("email2").value;

        var ctrl = $("#email2");
        var error = $("#errormessagename");

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
              // document.querySelector("#signup1").classList.add('disabled');

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

        $("#passwordRegister, #email2 , #name2, #passwordRegisterConfirm").on(
          "change input",
          function () {
            //btn.classList.add('disabled');
            const input = document.querySelector("#passwordRegister");
            const input1 = document.querySelector("#email2");
            const input2 = document.querySelector("#name2");
            const input3 = document.querySelector("#passwordRegisterConfirm");

            if (
              input.value == "" ||
              input3.value == "" ||
              input1.value == "" ||
              input2.value == "" ||
              input1.classList.contains("is-invalid")
            ) {
              document.querySelector("#signup1")?.classList.add("disabled");
            } else {
              document.querySelector("#signup1")?.classList.remove("disabled");
            }
          }
        );
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

            if (responseathu.success == "true") {
              Swal.fire({
                icon: "success",
                title: "Added!",
                text: "Connected successfully",
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
                title: "Error!",
                text: responseathu.message,
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
            // document.querySelector('#home-tab').click();
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
                text: "Check your email to reset your password",
                showConfirmButton: false,
                timer: 1500,
              });
              $("#forget-password-form")[0].reset();
            } else {
              Swal.fire({
                icon: "error",
                title: "Error!",
                text: "There`s no account associated with this email address",
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
          return urlParamuid;
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
                  title: "Updated!",
                  text: "Updated successfully",
                  showConfirmButton: false,
                  timer: 1500,
                });
                $("#reset-password-form")[0].reset();
                window.location.href = currentUrlresult;
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
    });
  });
