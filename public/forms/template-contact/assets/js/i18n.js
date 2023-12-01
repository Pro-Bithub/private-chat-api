var demoJson = {
	"demo": {
        "t-errormessages": {
			"en": "Error",
			"fr": "Erreur"
		},
        "errormessages": {
			"en": "An error has occurred. please try again another time.",
			"fr": "Une erreur s'est produite. veuillez réessayer une autre fois"
		},
        "t-successmessages": {
			"en": "Success",
			"fr": "Succès"
		},
        "successmessages": {
			"en": "Request sent successfully, we will get back to you as soon as possible",
			"fr": "Demande envoyée avec succès, nous vous reviendrons dans les plus brefs délais"
		},
       
       

        
		"title": {
			"en": "Chat Help Center",
			"fr": "Centre d’Aide à l’Utilisation du Chat"
		},
    "Sub-titles": {
			"en": "In order to process your request, make sure to fill in all required data",
			"fr": "Afin que votre requête soit traitée, veillez à renseigner l\’ensemble des données requises"

		},
		"pd": {
      "en": "Personal data",
			"fr": "Informations personnelles"
		},
    "rq": {
      "en": "Request",
			"fr":"Informations de votre demande"
		},


		"form": {
      
      "terms_agreement" :{
        "en": "By submitting your request you agree to be contacted by the appropriate service. The collect of personal data is for the sole purpose of processing your request",

			"fr":"En soumettant votre demande vous acceptez d'être recontacter par le service approprié. Le traitement des données personnelles recueillies a pour seul but le traitement de votre demande."
      },
			"name": {
				"pt": "Zé dos Anzóis",
				"en": "John Doe",
				"es": "Fulano de Tal"
			},
      "details":{
        "fr":"Entrez des détails sur votre demande.",
        "en":"Enter details about your request.",
      },
  

      "s-default-subject":{
        "fr":"Sélectionnez le sujet de la réclamation",
        "en":"Select claim subject",
      },

   
      "t-mail":{
        "fr":"Votre E-mail ",
        "en":"E-MAIL",
      },
      "t-firstname":{
        "fr":"Votre Prénom",
        "en":"First name",
      },

      "t-lastname":{
        "fr":"Votre Nom",
        "en":"Last name",
      },
      "lastname":{
        "fr":"dupon",
        "en":"smith",
      },


        "s-3-subject":{
        "fr":"Autre problème ",
        "en":"Other issue",
      },

          
       "s-2-subject":{
        "fr":"Je n’arrive pas à créer un compte",
        "en":"I don’t succeed in creating an account",
      },

      "s-1-subject":{
        "fr":"Je n’arrive pas à me connecter à mon compte",
        "en":"I don’t succeed in login to my account",
      },

      "t-subject":{
        "fr":"OBJET DE VOTRE DEMANDE",
        "en":"Subject",
      },
      "t-details":{
        "fr":"Détail de votre demande",
        "en":"Request detail",
      },

	  "firstnameP": {
		"en": "Please enter your first name",
		"fr": "Veuillez saisir votre prénom"
	},

	"lastnameP": {
		"en": "Please enter your last name",
		"fr": "Veuillez saisir votre nom"
	},
	"mailP": {
		"en":  "Please enter a valid email address",
		"fr": "Veuillez saisir une adresse email valide"
	},
	"objectP": {
		"en": "Please enter the subject of your request",
		"fr": "Veuillez saisir l'object de demande"
	},

	"detailsP": {
		"en": "Please provide a description of your request",
		"fr": "Veuillez décrire une description de votre demande"
	},
	"recontactP": {
		"en": "Please agree to be contacted before submitting your request.",
		"fr": "Veuillez accepter d'être recontacté avant de soumettre votre demande."
	},



   

    "firstname": {
				"en": "john",
				"fr": "jean"
			},

			"mail": {
				"en": "johnsmith@email.org",
				"fr": "jeandupont@email.org"
			},
			"submit": {
				"fr": "Envoyer la demande",
				"en": "Submit request"
			}
		}
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