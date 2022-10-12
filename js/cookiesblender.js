

document.addEventListener("DOMContentLoaded", function() {

    let setCookie = function (cname, cvalue, exdays = 182) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; path=/" + "; " + expires;
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
        }
    }

    // init answer from cookies;
    let cookieDialogUserAnswer = getCookie('cookieDialogAnswer');

    let setCookieDialogUserAnswered = function (){
        // setCookie('cookieDialogAnswer', true)
    }

    const cookiesBlenderDialog = function (startOpen = false){
        fetch(cookiesBlenderSiteUrl + '/wp-json/cookiesblender/v1/getscripts', {
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then((response) => {
            return response.json().then(function(json) {

                let content = '<table class="cookiesblender-table-list">';
                let checkbox = '<label class="cookiesblender-switch"><input type="checkbox"  name="accepted-cookieblenders" checked="checked" disabled value="required"  /><span class="cookiesblender-slider round"></span></label>';
                content+= '<tr><td>' + json.langs.RequiredCookies + '</td><td>' + checkbox + '</td></tr>';


                for (let item in json['cookiesList']) {
                    let cookiesItem = json['cookiesList'][item];
                    let checkbox = '<label class="cookiesblender-switch"><input type="checkbox"  name="accepted-cookieblenders" checked="checked" value="' + cookiesItem.cookieKey + '"  /><span class="cookiesblender-slider round"></span></label>';
                    content+= '<tr><td>' + cookiesItem.name + '</td><td>' + checkbox + '</td></tr>';
                }
                content+= '</table>';

                new Dialog({
                        title: json.langs.WeAreCookies,
                        content: content,
                        startOpen: startOpen,
                        onOpen: function(){
                            if(document.querySelectorAll('.cookiesblender-btn[data-btn-role="accept"]').length > 0){
                                document.querySelectorAll('.cookiesblender-btn[data-btn-role="accept"]')[0].focus();
                            }
                        },
                        onAccept: function(){
                            clearAllCookies();
                            setCookieDialogUserAnswered();

                            let checkedCookiesBlenders = document.querySelectorAll('input[type=checkbox][name=accepted-cookieblenders]:checked');
                            let acceptedCookiesBlenders = [];
                            for (let item in checkedCookiesBlenders) {
                                acceptedCookiesBlenders.push(checkedCookiesBlenders.value);
                            }


                            for (let item in json['cookiesList']) {
                                let cookiesItem = json['cookiesList'][item];
                                if(acceptedCookiesBlenders.includes(cookiesItem.cookieKey)){

                                }
                                else{

                                }
                            }

                            return true;
                        },
                        onCancel: function(){
                            clearAllCookies();
                            setCookieDialogUserAnswered();
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

    cookiesBlenderDialog(!cookieDialogUserAnswer);

    let button = document.createElement('span');
    button.setAttribute('id','cookiesblender-dialog-btn');
    button.addEventListener('click', function (event) {
        // Don't follow the link
        event.preventDefault();

        cookiesBlenderDialog(true);

    }, false);
    document.body.append(button);




});

