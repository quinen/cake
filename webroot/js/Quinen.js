/**
 *  Quinen : lib without any dependency
 *  Quinen.Cake : has multiple dependency : jquery, bootstrap, moment, select2
 *  Quinen.Html : lib to generate html elements, no dependency
 */
var Quinen = Quinen || {};
(function () {
    'use strict';

    this.copyToClipboard = function (v) {
        var d = document;
        var t = d.createElement("textarea");
        t.value = v;
        t.setAttribute("readonly", "");
        t.style.position = "absolute";
        t.style.left = "-9999px";
        d.body.appendChild(t);
        v = 0 < d.getSelection().rangeCount ? d.getSelection().getRangeAt(0) : !1;
        t.select();
        d.execCommand("copy");
        d.body.removeChild(t);
        v && (d.getSelection().removeAllRanges(), d.getSelection().addRange(v));
    };

    this.optionsDefault = function (obj, def) {
        obj = obj || {};
        for (let k in def) {
            if (typeof obj[k] === 'undefined') {
                obj[k] = def[k];
            }
        }
        return obj;
    };

    this.uuid = function () {
        return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c => (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16));
    };

    // "toto"   => ["toto",{}]
    // ["titi"] => ["titi",{}]
    // ["tata",{a:"b"}] => ["tata",{a:"b"}]
    // [["tutu","tete"]] => // [["tutu","tete"],{}]

    this.contentOptions = function (content, contentDefault) {
        let contentOptions = {};
        //if (typeof content === 'string') {} else
        if (Array.isArray(content)) {
            if (typeof content[1] !== 'undefined') {
                contentOptions = content[1];
            }
            content = content[0];
        }
        if (typeof content === 'undefined') {
            content = contentDefault
        }
        return [content, contentOptions];
    }
    ;

    this.template = (template, obj) => {
        return template.replace(new RegExp('\{\{([^}]+)\}\}', 'gi'), function () {
            if (typeof obj[arguments[1]] !== 'undefined') {
                return obj[arguments[1]];
            }
            return '';
        });
    };
}).apply(Quinen);

Quinen.Bs4 = Quinen.Bs4 || {};
(function () {
        'use strict';

        this.button = (button, options) => {
            let buttonDefault = 'light'
            let buttonColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark', 'link'];
            if (typeof this.buttonModels === 'undefined') {
                this.buttonModels = {};
                // generate from colors & outline
                [true, false].map((isOutline) => {
                        buttonColors.map((color) => {
                                let modelName = (isOutline ? 'outline-' : '') + color;
                                if (typeof this.buttonModels[modelName] === 'undefined') {
                                    this.buttonModels[modelName] = {
                                        color,
                                        isOutline
                                    };
                                }
                            }
                        );
                    }
                );
            }

            let buttonModels = {
                ...this.buttonModels,
                ...{
                    light: {
                        color: 'light',
                        isOutline: false
                    }
                }
            };

            button = button || buttonDefault;
            options = options || {};

            // model,contenu text direct
            if (typeof options === 'string') {
                options = {
                    text: options
                };
            }
            // options
            if (typeof button === 'object') {
                options = button;
            } else {
                options['button'] = button;
            }
            // button devient inutile ici

            // options OK

            // gestion des models
            while (typeof options['button'] !== 'undefined') {
                button = options['button'];
                delete options['button'];
                if (typeof buttonModels[button] !== 'undefined') {
                    options = Quinen.optionsDefault(options, buttonModels[button])
                } else {
                    options = Quinen.optionsDefault(options, buttonModels[buttonDefault])
                }
            }

            // create
            let btn = document.createElement('button');
            btn.classList.add('btn')

            // gestion des couleurs
            if (typeof options['color'] !== 'undefined') {
                if (buttonColors.indexOf(options['color']) !== -1) {
                    if (options['isOutline']) {
                        btn.classList.add('btn-outline-' + options['color']);
                    } else {
                        btn.classList.add('btn-' + options['color']);
                    }
                }
            }
            delete options['isOutline'];
            delete options['color'];

            // gestion du texte et icon
            btn.innerHTML = Quinen.Html.iconText(options).outerHTML;

            if (typeof options['isDisabled'] !== 'undefined' && options['isDisabled']) {
                options['disabled'] = 'disabled';
            }
            delete options['isDisabled'];

            Quinen.Html.setAttributes(btn, options);
            delete options['disabled'];

            return btn;
        };

        this.buttons = (buttons, options) => {
            let btns = document.createElement('div');
            btns.classList.add('btn-group');

            buttons.map((button) => {
                btns.appendChild(this.button.apply(null, button));
            });

            Quinen.Html.setAttributes(btns, options);
            return btns;
        };

    }
).apply(Quinen.Bs4);


Quinen.Cake = Quinen.Cake || {};
(function () {
    'use strict';

    this.onLoad = function () {
        this.listenOnCloseTabLink();
        //this.redirectPaginatorLink();
        this.updateFromInputValue();
        this.listenOnClearForm();
        this.listenOnDropdownMenu();
        this.initSelect2();
        this.initDatepicker();
        this.listenForLoading();
        this.initPopover();
        this.listenOnClickTabName();
        this.listenOnSubmitCloseTab();
        this.listenOnClickDataHref();
    };

    // ajax link
    this.onBeforeSendAjaxLink = function ($event, xhr) {
        // we store the old html for reprinting in case of
        var $link = $($event.currentTarget);
        $link.data('oldHtml', $link.html());
        // we run a spinner to indicate ajax call
        $link.html('<i class="fa fa-circle-notch fa-spin fa-lg"></i>');
    };

    this.onSuccessAjaxLink = function ($event, data, status, xhr) {
        // we restore the old html upon returning
        let $link = $($event.currentTarget);

        if (data.length > 0) {
            $link.html(data);
        } else {
            $link.html($link.data('oldHtml'));
        }

        if (typeof $event.currentTarget.dataset.renderResponse !== 'undefined') {
            $($event.currentTarget.dataset.renderResponse).html(xhr.responseText);
        }
        this.onLoad();
    };

    this.onErrorAjaxLink = function ($event, xhr, status, error) {
        if (xhr.status === 403) {
            document.location.reload(true);
        }
        var $link = $($event.currentTarget);
        var html = xhr.status + ' (' + error + ')';
        $link.html(html);
    };
    // end ajax link

    // tab link
    this.onBeforeSendTabLink = function ($event, xhr) {

        var $link = $($event.currentTarget);
        var id = $link.attr('id');

        // if tab already exist
        if ($("#t-" + id + "").length > 0) {
            if ($link.data('showOnClick') == "1") {
                $("#t-" + id + " > a").tab('show');
            }
            return false;

            /*
            if($link.data('confirm') && confirm($link.data('confirm')))
            {
                return true;
            }
            return $link.data('refreshOnClick');
            */
        }
        Quinen.Cake.onBeforeSendAjaxLink($event, xhr);
        return true;
    };

    this.addTab = function (tab, content, options) {
        options = Quinen.optionsDefault(options, {
            id: Quinen.uuid(),
            target: document,
            parent: 0,
            showOnClick: true
        });

        var target = $(options.target);

        // si tab n'existe pas deja
        if ($("#t-" + options.id + "").length === 0) {

            var $ul = target.parents('.card').eq(options.parent).find('ul.card-header-tabs:first');
            var $divs = $('#c-' + $ul.attr('id') + '');

            // on genere les tab et content
            var $li = $('<li class="nav-item" id="t-' + options.id + '">' +
                '<a href="#" data-toggle="tab" data-target="#c-' + options.id + '" class="nav-link">' +
                '<span>' + tab /* + target.data('oldHtml') */ + '</span>&nbsp;' +
                '<button data-remove="' + options.id + '" type="button" class="close" aria-label="Fermer">' +
                '<span aria-hidden="1"><i class="fa fa-times fa-fw"></i></span>' +
                '</button>' +
                '</a></li>'
            );

            var $div = $('<div class="tab-pane" id="c-' + options.id + '">' + content + '</div>');

            $ul.append($li);
            $divs.append($div);

            // titre
            var tabTitle = $('#c-' + options.id + '').find('.tab-link-title').html();
            if (typeof tabTitle !== "undefined" && tabTitle.length > 0) {
                $('#t-' + options.id + ' > a > span').html(tabTitle);
            }

            if (options.showOnClick) {
                $("#t-" + options.id + " > a").tab('show');
            }
        } else {
        }


    };

    this.onSuccessTabLink = function (event, data, status, xhr) {
        let $link = $(event.currentTarget);

        let content = (typeof data.content !== 'undefined' ? data.content : data);

        Quinen.Cake.addTab(
            $link.data('oldHtml'),
            content,
            {
                id: $link.attr('id'),
                target: event.currentTarget,
                parent: $link.data('parent'),
                showOnClick: $link.data('showOnClick')
            }
        );

        Quinen.Cake.onSuccessAjaxLink(event, '', status, xhr);
    };


    this.onErrorTabLink = function (event, xhr, status, error) {
        console.dir(arguments);
        var $link = $(event.currentTarget);

        Quinen.Cake.addTab(
            xhr.status + ' (' + error + ')',
            xhr.responseText,
            {
                id: $link.attr('id'),
                target: event.currentTarget,
                parent: $link.data('parent'),
                showOnClick: $link.data('showOnClick')
            }
        );

        Quinen.Cake.onErrorAjaxLink(event, xhr, status, error);
    };


    this.listenOnCloseTabLink = function () {

        $('ul.nav-tabs').on('click', 'li a .close', function () {

            // id to remove
            var $id = $(this).data('remove');
            var $ul = $(this).parents('ul:first');

            $('#t-' + $id + '').remove();
            $('#c-' + $id + '').remove();

            // on se place sur le dernier
            $ul.find('li:last-child a').tab('show');

        });
    };
    // end tab link

    // tr link
    this.onBeforeSendTrLink = function ($event, xhr) {

        var $link = $($event.currentTarget);

        // on va cacher le contenu
        if ($link.data('isNextTr')) {
            $link.data('isNextTr', false);
            $link.closest('tr').next().remove();

            if (typeof $link.data('newTrHtml') !== 'undefined') {
                $link.data('oldHtml', $link.data('oldTrHtml'));
                Quinen.Cake.onSuccessAjaxLink($event, '', null, xhr);
            }
            return false;
        } else {
            // on va afficher le contenu
            Quinen.Cake.onBeforeSendAjaxLink($event, xhr);
        }
    };

    this.onSuccessTrLink = function ($event, data, status, xhr) {
        var $link = $($event.currentTarget);
        var $tr = $link.closest('tr');
        var nbChild = $tr.children().length;

        $link.data('isNextTr', true);
        $tr.after('<tr><td colspan="' + nbChild + '">' + data + '</td></tr>');

        // if newTrHtml setted
        // store oldHtml to oldTrHtml
        // copy newTrHtml to oldHtml
        if (typeof $link.data('newTrHtml') !== 'undefined') {
            $link.data('oldTrHtml', $link.data('oldHtml'));
            $link.data('oldHtml', $link.data('newTrHtml'));
        }

        Quinen.Cake.onSuccessAjaxLink($event, '', status, xhr);
        return true;
    };
    // end tr link

    this.onBeforeSendModalLink = function ($event, xhr) {
        var $link = $($event.currentTarget);

        Quinen.Cake.onBeforeSendAjaxLink($event, xhr);

        if ($link.data('size')) {
            $('#linkModal').find('.modal-dialog').addClass('modal-' + $link.data('size'));
        }
    };

    this.onSuccessModalLink = function ($event, data, status, xhr) {

        $('#linkModal').find('.modal-body').html(data);
        $('#linkModal').modal({
            //backdrop: 'static'
        });
        Quinen.Cake.onSuccessAjaxLink($event, '', status, xhr);
        return true;
    };

    this.redirectPaginatorLink = function () {
        $(document).on('click', '.paginator-container a', function () {
            var $div = $(this).parents('.paginator-container:first');
            $div.append(
                '<div class="w-100 h-100 text-center position-absolute bg-secondary"' +
                ' style="top:0;opacity: .5">' +
                '<br/>' + '<br/>' + '<br/>' +
                '<i class="fa fa-circle-notch fa-spin fa-10x"></i>' +
                '</div>'
            );


            var thisHref = $(this).attr('href');
            if (!thisHref || thisHref == '#') {
                return false;
            }

            $('.paginator-container').load(thisHref, function () {
                var scripts = this.getElementsByTagName('script');
                for (var i = 0; i < scripts.length; i++) {
                    if (typeof scripts[i] !== 'undefined') {
                        $.globalEval(scripts[i].innerHTML);
                    }
                }
            });
            return false;
        });
    };

    this.drawChart = function () {

        $('[data-chart]').each(function (i, canvas) {
            var ctx = canvas.getContext('2d');
            return new Chart(ctx, JSON.parse(canvas.dataset.chart));
        });
    };

    this.initSelect2 = function () {

        $('select').select2({
            theme: 'bootstrap4',
            language: 'fr',
            allowClear: true,
            placeholder: "",
            dropdownAutoWidth: true,
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function (data) {
                // return html if exist else return legacy text
                return data.html || data.text;
            },
            templateSelection: function (data) {
                return data.text;
            }

            //allowClear: true // bootstrap 4 conflict
        }).on('select2:opening', function () {
            Quinen.Cake.showLoading = false;
        }).on('select2:close', function () {
            Quinen.Cake.showLoading = true;
        }).maximizeSelect2Height();
    };


    this.updateFromInputValue = function () {
        $('[data-from-input-value]').each(function (i, tag) {
            var fieldName = this.dataset.fromInputValue;
            var input = document.getElementById(fieldName.replace(/[._]/g, '-'));
            $(input).keyup(function (e) {
                tag.innerHTML = e.target.value;
            });
        });
    };

    this.listenOnClearForm = function () {
        $('[data-clear-form]').click(function (event) {
            var inputs = event.target.form.elements;
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs.item(i);

                switch (input.type) {
                    case 'hidden':
                        switch (input.name) {
                            case '_method':
                            case '_csrfToken':
                            default:
                            // rien
                        }
                        break;
                    case 'radio':
                        input.checked = false;
                    default:
                        input.value = '';
                }
            }
        });
    };

    this.listenOnDropdownMenu = function () {
        $('.dropdown-menu .dropdown-toggle').on('click', function (e) {
            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            var $subMenu = $(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');


            $(this).parents('.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
                $('.dropdown-submenu .show').removeClass("show");
            });

            return false;
        });
    };

    this.delayedMap = function (arr, delay, fn) {
        var index = 0;

        function next() {
            // protect against empty array
            if (!arr.length) {
                return;
            }

            // see if we need to wrap the index
            if (index >= arr.length) {
                return;
                // infinite
                //index = 0;
            }

            // call the callback
            if (fn(arr[index], index, arr) === false) {
                // stop iterating
                return;
            }

            ++index;

            // schedule next iteration
            setTimeout(next, delay);
        }

        // start the iteration
        //next();
        setTimeout(next, delay);
    };

    this.getDatePlaceholder = function (locale) {
        var localeData = moment().locale(locale)._locale;
        var format = localeData._longDateFormat.L
        var D = localeData._relativeTime.dd.split(' ')[1][0];
        var M = localeData._relativeTime.MM.split(' ')[1][0];
        var Y = localeData._relativeTime.yy.split(' ')[1][0];
        return format.replace(/D/g, D).replace(/M/g, M).replace(/Y/g, Y);
    };

    this.initDatepicker = function () {

        var locale = 'fr';
        var placeholder = Quinen.Cake.getDatePlaceholder(locale);

        $('[data-toggle="datetimepicker"]').each(function () {
            var $source = $(this);
            var $target = $($source[0].dataset.date);

            // init from hidden
            var init = null;
            if ($target.val().length) {
                init = new Date($target.val());
            }

            $source.val(moment(init).format('L'));
            $source.attr('placeholder', placeholder);

            $source.datetimepicker({
                format: 'L',
                //defaultDate: init,
                locale: locale
            });

            $source.on('change.datetimepicker', function (event) {
                var formatted = '';
                if (typeof event.date !== "undefined") {
                    formatted = event.date.toISOString(true).split('T')[0];
                }
                $target.val(formatted);
            });

            $target.change(function (event) {

            });
        });


    }

    this.startLoading = function () {
        if (Quinen.Cake.showLoading) {
            $('#loadingModal').modal({
                backdrop: 'static'
            });
            $('#loadingProgress').show();
        }
    };

    this.stopLoading = function () {
        $('#loadingModal').modal('hide');
        $('#loadingProgress').hide();
    };

    this.showLoading = true;

    this.listenForLoading = function () {
        var self = this;
        $(document).ajaxStart(function () {
            self.startLoading();
        }).ajaxStop(function () {
            self.stopLoading();
        });

        $('form').submit(function () {
            self.startLoading();
        })
    };

    this.initPopover = function () {
        $('[data-toggle="popover"]').popover();

        $('[data-toggle="popover"]').on('shown.bs.popover', function () {
            setTimeout(function () {
                $('[data-toggle="popover"]').popover('hide');
            }, 1000);
        });
    };

    // synchronise les sous onglet ayant le meme nom
    this.listenOnClickTabName = function () {
        $('a[data-toggle]').on('shown.bs.tab', function (event) {
            // ouvre les onglets ayant le meme nom
            let name = event.target.dataset['name'];
            $('a[data-name="' + name + '"]').tab('show');
            // render ajax
            let contentDiv = document.querySelectorAll(event.target.dataset['target'])[0];
            let contentUrl = contentDiv.dataset['content'];
            if (typeof contentUrl !== 'undefined' && contentDiv.dataset['rendered'] !== "true") {
                // render inside div
                $.ajax({
                    url: contentUrl,
                }).done(function (data, status, xhr) {
                    contentDiv.innerHTML = data;
                    contentDiv.dataset['rendered'] = true;
                }).fail(function (xhr, status, error) {
                    //console.log(arguments);
                    contentDiv.innerHTML = xhr.responseText;
                });

            }
        })
    };

    this.renderFlash = function (_message) {
        $('.alert-dismissible').remove();
        return _message.map(function (msg) {
            var type = msg.params.class || 'danger';
            return '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">\n' +
                msg.message +
                '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                '    <span aria-hidden="true">&times;</span>\n' +
                '  </button>\n' +
                '</div>';
        }).join('');

    }
    // 'data-close-tab-on-submit' => 1
    this.listenOnSubmitCloseTab = function () {
        var that = this;
        $('form[data-close-tab-on-submit=1]').on('submit', function (event) {
            var form = $(this);
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                },
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize()
            }).done(function (response, textStatus, xhr) {
                form.parents('.container-fluid:first').prepend(Quinen.Cake.renderFlash(response._message));
                var paneId = form.parents('.tab-pane:first').attr('id');
                $('a[data-target="#' + paneId + '"] .close').click();
            }).fail(function (response) {
                form.parents('.container-fluid:first').prepend(Quinen.Cake.renderFlash(response._message));
                // Optionally alert the user of an error here...
                console.dir(arguments);
                form.prepend(response.content);
            });


            that.stopLoading();
            event.preventDefault();
            event.stopPropagation();

        });
    };

    this.listenOnClickDataHref = function () {
        $('a[data-href]').on('click', function (event) {

            let btn = event.currentTarget;
            var link = btn.dataset.link || 'ajax';
            link = link.charAt(0).toUpperCase() + link.slice(1);

            $.ajax({
                url: btn.dataset.href,
                method: btn.dataset.method || 'GET',
                data: btn.dataset.data || {},
                beforeSend: function (xhr, settings) {
                    Quinen.Cake['onBeforeSend' + link + 'Link'].apply(Quinen.Cake, [event, xhr])
                }
            }).done(function (data, status, xhr) {
                Quinen.Cake['onSuccess' + link + 'Link'].apply(Quinen.Cake, [event, data, status, xhr])
            }).fail(function (xhr, status, error) {
                console.log(link, 'fail');
                Quinen.Cake['onError' + (link === 'Tab' ? link : 'Ajax') + 'Link'].apply(Quinen.Cake, [event, xhr, status, error])
            })
        });
    }

}).apply(Quinen.Cake);

Quinen.Html = Quinen.Html || {};
(function () {
    'use strict';
    this.table = function (data, maps, options) {
        data = data || [];
        maps = maps || [];
        options = options || {};

        Quinen.optionsDefault(options, {
            showHead: true
        });

        let table = document.createElement('table');

        var initMaps = function (maps, data) {
            if (maps.length === 0) {
                maps = Object.keys(data[0]);
            }
            return maps.map(function (map) {
                if (typeof map === 'string') {
                    map = {
                        label: map,
                        field: map,
                    }
                } else if (typeof map === 'object') {// already ok
                } else {
                    throw 'invalid map : (' + (typeof map) + ')' + map;
                }

                map.field = Quinen.contentOptions(map.field);
                map.label = Quinen.contentOptions(map.label, map.field[0]);

                return map;
            });
        }

        //table.classList.add('table');
        let thead = (table, maps) => {
            let thead = table.createTHead();
            let row = thead.insertRow();

            for (let map of maps) {
                if (typeof map.isNewLine !== 'undefined' && map.isNewLine === true) {
                    row = thead.insertRow();
                }
                let th = document.createElement("th");
                let text = document.createTextNode(map.label[0]);
                // label options
                this.setAttributes(th, map.label[1]);
                th.appendChild(text);
                row.appendChild(th);
            }
        };

        let tbody = (table, data, maps) => {
            for (let line of data) {
                let tr = table.insertRow();
                for (let map of maps) {
                    if (typeof map.isNewLine !== 'undefined' && map.isNewLine === true) {
                        tr = table.insertRow();
                    }
                    let td = tr.insertCell();
                    //let text = document.createTextNode(line[key]);
                    //td.appendChild(text);

                    // get scalar value
                    let wasArray = false;
                    let value = [line[map.field[0]]];
                    // array value
                    if (Array.isArray(map.field[0])) {
                        wasArray = true;
                        value = (map.field[0]).map(function (field) {
                            return line[field];
                        });
                    }

                    // format
                    if (typeof map.format !== 'undefined') {
                        value = map.format.apply(null, value);
                        if (!wasArray && !Array.isArray(value)) {
                            value = [value];
                        }
                    }

                    let valueOptions = {};
                    [value, valueOptions] = Quinen.contentOptions((wasArray ? value : value[0]));
                    Quinen.optionsDefault(valueOptions, map.field[1]);

                    td.innerHTML = value;
                    // td options
                    this.setAttributes(td, valueOptions);
                }
            }
        };

        maps = initMaps(maps, data);
        tbody(table, data, maps);
        if (options['showHead']) {
            thead(table, maps);
        }
        delete options['showHead'];

        // set options on table
        this.setAttributes(table, options);
        return table;
    };

    this.setAttributes = function (obj, options) {
        for (let option in options) {
            if (options.hasOwnProperty(option)) {
                obj.setAttribute(option, options[option]);
            }
        }
    };

    this.tag = function (tagName, content, options) {
        tagName = tagName || 'div';
        content = content || null;
        options = options || {};

        let tag = document.createElement(tagName);
        tag.innerHTML = content;
        this.setAttributes(tag, options);
        return tag;
    };

    this.icon = (iconName, options) => {
        let iconTypes = {
            brand: 'fab',
            light: 'fal'
        };
        let iconBrands = ['maxcdn'];

        iconName = iconName || 'question';
        options = options || {};

        if (typeof iconName === 'object') {
            options = iconName;
            iconName = '';
        }

        Quinen.optionsDefault(options, {
            icon: iconName,
            type: 'light'
        });

        let icon = document.createElement('i');
        // is brand ?
        if (iconBrands.indexOf(options['icon']) !== -1) {
            options['type'] = 'brand';
        }
        // type
        if (typeof iconTypes[options['type']] !== 'undefined') {
            icon.classList.add(iconTypes[options['type']]);
        }
        delete options['type'];

        icon.classList.add('fa-' + options['icon']);
        delete options['icon'];

        return icon;
    }
    ;

    this.iconText = (iconName, text, options) => {
        text = text || '';
        options = options || {};

        if (typeof iconName === 'object') {
            options = iconName,
                iconName = false
        }

        Quinen.optionsDefault(options, {
            icon: iconName,
            showIcon: true,
            text: text,
            showText: true,
            template: '{{icon}} {{text}}'
        });

        if (options['showIcon'] && options['icon']) {
            // transform iconName in icon
            options['icon'] = this.icon.apply(null, Quinen.contentOptions(options['icon'])).outerHTML;
        } else {
            delete options['icon'];
        }

        if (!options['showText']) {
            delete options['text'];
        }

        let span = document.createElement('span');

        span.innerHTML = Quinen.template(options['template'], options);

        delete options['showIcon'];
        delete options['icon'];
        delete options['showText'];
        delete options['text'];
        delete options['template'];

        return span;
    }


}).apply(Quinen.Html);

$(function () {
    Quinen.Cake.onLoad();
});
$(document).ajaxStop(function () {
    Quinen.Cake.initSelect2();
});

moment.updateLocale('fr', {
    months: 'janvier_février_mars_avril_mai_juin_juillet_août_septembre_octobre_novembre_décembre'.split('_'),
    monthsShort: 'janv._févr._mars_avr._mai_juin_juil._août_sept._oct._nov._déc.'.split('_'),
    monthsParseExact: true,
    weekdays: 'dimanche_lundi_mardi_mercredi_jeudi_vendredi_samedi'.split('_'),
    weekdaysShort: 'dim._lun._mar._mer._jeu._ven._sam.'.split('_'),
    weekdaysMin: 'di_lu_ma_me_je_ve_sa'.split('_'),
    weekdaysParseExact: true,
    longDateFormat: {
        LT: 'HH:mm',
        LTS: 'HH:mm:ss',
        L: 'DD/MM/YYYY',
        LL: 'D MMMM YYYY',
        LLL: 'D MMMM YYYY HH:mm',
        LLLL: 'dddd D MMMM YYYY HH:mm'
    },
    calendar: {
        sameDay: '[Aujourd’hui à] LT',
        nextDay: '[Demain à] LT',
        nextWeek: 'dddd [à] LT',
        lastDay: '[Hier à] LT',
        lastWeek: 'dddd [dernier à] LT',
        sameElse: 'L'
    },
    relativeTime: {
        future: 'dans %s',
        past: 'il y a %s',
        s: 'quelques secondes',
        ss: '%d secondes',
        m: 'une minute',
        mm: '%d minutes',
        h: 'une heure',
        hh: '%d heures',
        d: 'un jour',
        dd: '%d jours',
        M: 'un mois',
        MM: '%d mois',
        y: 'un an',
        yy: '%d ans'
    },
    dayOfMonthOrdinalParse: /\d{1,2}(er|)/,
    ordinal: function (number, period) {
        switch (period) {
            // TODO: Return 'e' when day of month > 1. Move this case inside
            // block for masculine words below.
            // See https://github.com/moment/moment/issues/3375
            case 'D':
                return number + (number === 1 ? 'er' : '');

            // Words with masculine grammatical gender: mois, trimestre, jour
            default:
            case 'M':
            case 'Q':
            case 'DDD':
            case 'd':
                return number + (number === 1 ? 'er' : 'e');

            // Words with feminine grammatical gender: semaine
            case 'w':
            case 'W':
                return number + (number === 1 ? 're' : 'e');
        }
    },
    week: {
        dow: 1, // Monday is the first day of the week.
        doy: 4  // The week that contains Jan 4th is the first week of the year.
    }
});