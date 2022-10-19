

document.addEventListener("DOMContentLoaded", function() {

    let setCookie = function (cname, cvalue, exdays = 182) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; path=/" + "; " + expires;
    }

    // Sticking with broadly-supported features:
    let insertJsRawScriptIntoDom  = function (htmlString){

        targetId = 'cookiesblender-scripts-temporary';

        if(document.getElementById(targetId) == undefined){
            let targetDom = document.createElement("div");
            targetDom.id = targetId;
            document.body.appendChild(targetDom);
        }

        let target = document.getElementById(targetId);
        target.insertAdjacentHTML("beforeend", htmlString);
        let scripts = target.getElementsByTagName("script");
        while (scripts.length) {
            let script = scripts[0];
            script.parentNode.removeChild(script);
            let newScript = document.createElement("script");
            if (script.src) {
                newScript.src = script.src;
            } else if (script.textContent) {
                newScript.textContent = script.textContent;
            } else if (script.innerText) {
                newScript.innerText = script.innerText;
            }
            document.body.appendChild(newScript);
        }
    }

    let getCookie = function (cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }


    const clearAllCookies  = () => {
        const cookies = document.cookie.split(";");

        for (const cookie of cookies) {
            const eqPos = cookie.indexOf("=");
            const name = eqPos > -1 ? cookie.substring(0, eqPos) : cookie;
            setCookie(name, null, -1);
            console.log('clear cookie : ' + name);
        }
    }

    // init answer from cookies;
    let cookieDialogUserAnswer = getCookie('cookieDialogAnswer');

    let setCookieDialogUserAnswered = function (){
        setCookie('cookieDialogAnswer', true)
    }

    let setCookieBlenderConsentStatus = function (cookieKey, status){
        setCookie('cookiesblender_consent_' + cookieKey, status);
    }

    const cookiesBlenderDialog = function (startOpen = false){
        fetch(cookiesBlenderParams.siteUrl + '/wp-json/cookiesblender/v1/getscripts', {
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then((response) => {
            return response.json().then(function(json) {

                 let content = '<div class="dilog-content">' + json.dialogContent + '</div>';
                content+= '<table class="cookiesblender-table-list">';

                // let checkbox = '<label class="cookiesblender-switch"><input type="checkbox"  name="accepted-cookieblenders" checked="checked" disabled value="required"  /><span class="cookiesblender-slider round"></span></label>';

                // content+= '<tr><td>' + json.langs.RequiredCookies + ' (*)</td><td>' + checkbox + '</td></tr>';

                for (let item in json['cookiesList']) {
                    let cookiesItem = json['cookiesList'][item];

                    let moreAttribute = '';
                    if(cookiesItem.accepted != 0 || 'required' == cookiesItem.cookieKey){
                        moreAttribute+= ' checked="checked" ';
                    }

                    if('required' == cookiesItem.cookieKey){
                        cookiesItem.name+= ' (*)';
                        moreAttribute+= ' disabled ';
                    }

                    let checkbox = '<label class="cookiesblender-switch"><input type="checkbox"  name="accepted-cookieblenders" ' + moreAttribute + ' value="' + cookiesItem.cookieKey + '"  /><span class="cookiesblender-slider round"></span></label>';

                    let showMoreBtn = '';
                    if(cookiesItem.decription.length > 0){
                        showMoreBtn = ' <small class="cookiesblender-show-description --close" data-cookies="' + cookiesItem.cookieKey + '" >' + json.langs.ShowDetails + '</small>';
                    }
                    content+= '<tr><td>' + cookiesItem.name + showMoreBtn + '</td><td>' + checkbox + '</td></tr>';

                    if(cookiesItem.decription.length > 0) {
                        content += '<tr class="cookiesblender-description-toggle --close" data-cookies="' + cookiesItem.cookieKey + '" ><td colspan="2">' + cookiesItem.decription + '</td></tr>';
                    }

                }
                content+= '</table>';

                content+= '<div><small>* ' + json.langs.optionWithStartAreRequired + '</small></div>';

                if(json.pages.privacyPolicy.length > 0) {
                    content += '<div><small><a href="' + json.pages.privacyPolicy + '">' + json.langs.CheckPrivacyPolicyPage + '</a></small></div>';
                }

                new CookiesBlenderDialog({
                        title: json.langs.WeAreCookies,
                        content: content,
                        startOpen: startOpen,
                        onOpen: function(){
                            if(document.querySelectorAll('.cookiesblender-btn[data-btn-role="accept"]').length > 0){
                                document.querySelectorAll('.cookiesblender-btn[data-btn-role="accept"]')[0].focus();
                            }
                        },
                        onInit: function(dialogobj){
                            let moreInfosBtn = dialogobj.dialog.querySelectorAll('.cookiesblender-show-description');
                            moreInfosBtn.forEach(el => el.addEventListener('click', event => {
                                let dataCookies = event.target.getAttribute("data-cookies");
                                if(dataCookies.length > 0){
                                    let classToRemove = '--open';
                                    let classToAdd = '--close';
                                    if(el.classList.contains('--close')){
                                        classToRemove = '--close';
                                        classToAdd =  '--open';
                                    }

                                    el.classList.remove(classToRemove);
                                    el.classList.add(classToAdd);


                                    let descBlocks = dialogobj.dialog.querySelectorAll('.cookiesblender-description-toggle[data-cookies="'+dataCookies+'"]');
                                    descBlocks.forEach(function(item){
                                        item.classList.remove(classToRemove);
                                        item.classList.add(classToAdd);
                                    });


                                }
                            }));
                        },
                        onAccept: function(){
                            clearAllCookies();
                            setCookieDialogUserAnswered();

                            let checkedCookiesBlenders = document.querySelectorAll('input[type=checkbox][name=accepted-cookieblenders]:checked');

                            let acceptedCookiesBlenders = [];

                            for (i = 0; i < checkedCookiesBlenders.length; ++i) {
                                acceptedCookiesBlenders.push(checkedCookiesBlenders[i].value);
                            }

                            for (i = 0; i < json['cookiesList'].length; ++i) {
                                let cookiesItem = json['cookiesList'][i];

                                if(acceptedCookiesBlenders.includes(cookiesItem.cookieKey)){;
                                    // add consent
                                    setCookieBlenderConsentStatus(cookiesItem.cookieKey , 1);

                                    // Load script into page
                                    if(!cookiesBlenderParams.activeOnThisPage.includes(cookiesItem.cookieKey)){
                                        if(cookiesItem.content.length > 0){
                                            cookiesBlenderParams.activeOnThisPage.push(cookiesItem.cookieKey);
                                            insertJsRawScriptIntoDom('<!-- Set cookies blender params -->' + "\r\n"  + cookiesItem.content);
                                        }
                                    }
                                }
                                else{
                                    // Remove consent
                                    setCookieBlenderConsentStatus(cookiesItem.cookieKey, 0);

                                    console.log('setCookieBlenderConsentStatus not accepted for '+ cookiesItem.cookieKey);
                                }
                            }

                            return true;
                        },
                        onRefuse: function(){
                            clearAllCookies();
                            setCookieDialogUserAnswered();
                            return true;
                        },
                        onClose : function (){
                            return true;
                        }
                    },
                    {
                        Cancel : 'Refuser',
                        Ok : 'Ok',
                    }
                );
            });
        })
        .then(function(json) {
            // console.log(json);
        });
    }

    if(!cookieDialogUserAnswer) {
        cookiesBlenderDialog(!cookieDialogUserAnswer);
    }

    let button = document.createElement('span');
    button.setAttribute('id','cookiesblender-dialog-btn');
    button.setAttribute('title',cookiesBlenderParams.langs.cookiesButtonTitle);
    button.addEventListener('click', function (event) {
        // Don't follow the link
        event.preventDefault();
        cookiesBlenderDialog(true);
    }, false);
    document.body.append(button);

});