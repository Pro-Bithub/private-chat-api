var demoJson = {
	"demo": {
		"welcomeMessage": {
			"en": "Welcome to ",
			"fr": "Bienvenue sur "
		},
		"loginMessage": {
			"en": "Login",
			"fr": "Connexion  "
		},

		"registerMessage": {
			"en": "Register",
			"fr": "S'inscrire"
		  },

		  "registerMessage": {
			"en": "Register",
			"fr": "S'inscrire"
		  },

		  "signIn": {
			"en": "Sign In",
			"fr": "Se Connecter"
		  },
		  "signUp": {
			"en": "Sign Up",
			"fr": "S'inscrire"
		  },
		
		  "userName": {
			"en": "User Name",
			"fr": "Nom d'utilisateur"
		  },
		  "emailAddress": {
			"en": "Email Address",
			"fr": "Adresse E-mail"
		  },
		  "password": {
			"en": "Password",
			"fr": "Mot de passe"
		  },
		  "confirmPassword": {
			"en": "Confirm Password",
			"fr": "Confirmer le mot de passe"},
			"show": {
				"en": "Show",
				"fr": "afficher"}
				,
				"signInToAccount": {
					"en": "Sign Into Your Account",
					"fr": "Connectez-vous à votre compte"
				  },
				  "createAccount": {
					"en": "Create an Account",
					"fr": "Créer un compte"
				  },
				  "termsAndConditions": {
					"en": "Terms & Conditions",
					"fr": "Termes et conditions"
				  },
				  "contactUs": {
					"en": "Contact us",
					"fr": "Contactez-nous"
				  },

				  "forgetPassword": {
					"en": "Forgot Password?",
					"fr": "Mot de passe oublié ?"
				},

				"emailisalready": {
					"en": "Email is already used !",
					"fr": "L'email est déjà utilisé !"
				},
				"loading": {
					"en": "loading..",
					"fr": "chargement.."
				},

				
				"forgetPasswordtitler": {
					"en": "Private-chat | Forgot password",
					"fr": "Private-chat | Mot de passe oublié"
				},
	
					
				"forgetPasswordicon": {
					"en": "Forgot Password? 🔒",
					"fr": "Mot de passe oublié? 🔒"
				},
				"resetPasswordInstructions": {
					"en": "Enter your email and we'll send you instructions to reset your password",
					"fr": "Entrez votre adresse e-mail et nous vous enverrons des instructions pour réinitialiser votre mot de passe"
				  },
				  "sendResetLink": {
					"en": "Send Reset Link",
					"fr": "Envoyer le lien de réinitialisation"
				  },
				  "backToLogin": {
					"en": "Back to login",
					"fr": "Retour à la connexion"
				  },
				  "resetPasswordTitle": {
					"en": "Reset Password? 🔒",
					"fr": "Réinitialiser le mot de passe ? 🔒"
				  },
				  "enterNewPassword": {
					"en": "Enter your new password",
					"fr": "Entrez votre nouveau mot de passe"
				  },
				  "reset": {
					"en": "Reset",
					"fr": "Réinitialiser"
				  },

	}
};


(function () {
	this.I18n = function (defaultLang) {
		var lang = defaultLang || 'en';
		this.language = lang;

		(function (i18n) {
			i18n.contents = demoJson;
			i18n.contents.prop = function (key) {
				var result = this;
				var keyArr = key.split('.');
				for (var index = 0; index < keyArr.length; index++) {
					var prop = keyArr[index];
					result = result[prop];
				}
				return result;
			};
			i18n.localize();
		})(this);
	};

	this.I18n.prototype.hasCachedContents = function () {
		return this.contents !== undefined;
	};

	this.I18n.prototype.lang = function (lang) {
		if (typeof lang === 'string') {
			this.language = lang;
		}
		this.localize();
		return this.language;
	};

	this.I18n.prototype.localize = function () {
		var contents = this.contents;
		if (!this.hasCachedContents()) {
			return;
		}
		var dfs = function (node, keys, results) {
			var isLeaf = function (node) {
				for (var prop in node) {
					if (node.hasOwnProperty(prop)) {
						if (typeof node[prop] === 'string') {
							return true;
						}
					}
				}
			}
			for (var prop in node) {
				if (node.hasOwnProperty(prop) && typeof node[prop] === 'object') {
					var myKey = keys.slice();
					myKey.push(prop);
					if (isLeaf(node[prop])) {
						//results.push(myKey.reduce((prev, current) => prev + '.' + current));	//not supported in older mobile broweser
						results.push(myKey.reduce( function (previousValue, currentValue, currentIndex, array) {
							return previousValue + '.' + currentValue;
						}));
					} else {
						dfs(node[prop], myKey, results);
					}
				}
			}
			return results;
		};
		var keys = dfs(contents, [], []);
		for (var index = 0; index < keys.length; index++) {
			var key = keys[index];
			if (contents.prop(key).hasOwnProperty(this.language)) {
				$('[data-i18n="'+key+'"]').text(contents.prop(key)[this.language]);
				$('[data-i18n-placeholder="'+key+'"]').attr('placeholder', contents.prop(key)[this.language]);
				$('[data-i18n-value="'+key+'"]').attr('value', contents.prop(key)[this.language]);
			} else {
				$('[data-i18n="'+key+'"]').text(contents.prop(key)['en']);
				$('[data-i18n-placeholder="'+key+'"]').attr('placeholder', contents.prop(key)['en']);
				$('[data-i18n-value="'+key+'"]').attr('value', contents.prop(key)['en']);
			}
		}
	};

}).apply(window);