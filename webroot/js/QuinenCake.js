var QuinenCake = QuinenCake || {};

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
        //console.debug('onSuccessAjaxLink', data.length, status);
        // we restore the old html upon returning
        var $link = $($event.currentTarget);
        if (data.length > 0) {
            $link.html(data);
        } else {
            $link.html($link.data('oldHtml'));
        }
    };

    this.onErrorAjaxLink = function ($event, xhr, status, error) {
        if (xhr.status == 403) {
            document.location.reload(true);
        }
        var $link = $($event.currentTarget);
        // trop radical
        //$link.html(xhr.responseText);
        $link.html(xhr.status + ' (' + error + ')');
    };
    // end ajax link

    // tab link
    this.onBeforeSendTabLink = function ($event, xhr) {
        //console.log('onBeforeSendTabLink',event,xhr);

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
        QuinenCake.onBeforeSendAjaxLink($event, xhr);
        return true;
    };

    this.onSuccessTabLink = function ($event, data, status, xhr) {
        //console.debug('onSuccessTabLink', data.length, status);
        var $link = $($event.currentTarget);
        var id = $link.attr('id');

        // si tab n'existe pas deja
        if ($("#t-" + id + "").length == 0) {
            var $ul = $link.parents('.card').eq($link.data('parent')).find('ul.card-header-tabs:first');
            var $divs = $('#c-' + $ul.attr('id') + '');

            // on genere les tab et content
            var $li = $('<li class="nav-item" id="t-' + id + '">' +
                '<a href="#" data-toggle="tab" data-target="#c-' + id + '" class="nav-link">' +
                '<span>' + $link.data('oldHtml') + '</span>&nbsp;' +
                '<button data-remove="' + id + '" type="button" class="close" aria-label="Fermer">' +
                '<span aria-hidden="1"><i class="fa fa-times fa-fw"></i></span>' +
                '</button>' +
                '</a></li>'
            );

            var $div = $('<div class="tab-pane" id="c-' + id + '">' + data + '</div>');

            $ul.append($li);
            $divs.append($div);

            // titre
            var tabTitle = '';
            tabTitle = $('#c-' + id + '').find('.tab-link-title').html();
            if (typeof tabTitle !== "undefined" && tabTitle.length > 0) {
                $('#t-' + id + ' > a > span').html(tabTitle);
            }

            if ($link.data('showOnClick')) {
                $("#t-" + id + " > a").tab('show');
            }
            this.updateFromInputValue()
        } else {
            //console.debug('onSuccessTabLink', "#t-" + id + "");
        }

        QuinenCake.onSuccessAjaxLink($event, '', status, xhr);
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
                QuinenCake.onSuccessAjaxLink($event, '', null, xhr);
            }
            return false;
        } else {
            // on va afficher le contenu
            QuinenCake.onBeforeSendAjaxLink($event, xhr);
        }
    };

    this.onSuccessTrLink = function ($event, data, status, xhr) {
        //console.debug('onSuccessTrLink', data.length);
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

        QuinenCake.onSuccessAjaxLink($event, '', status, xhr);
        return true;
    };
    // end tr link

    this.onBeforeSendModalLink = function ($event, xhr) {
        //console.log('onBeforeSendModalLink');
        var $link = $($event.currentTarget);

        QuinenCake.onBeforeSendAjaxLink($event, xhr);

        if ($link.data('size')) {
            $('#linkModal').find('.modal-dialog').addClass('modal-' + $link.data('size'));
        }
    };

    this.onSuccessModalLink = function ($event, data, status, xhr) {
        //console.log('onSuccessModalLink', data.length);
        QuinenCake.onSuccessAjaxLink($event, '', status, xhr);
        $('#linkModal').find('.modal-body').html(data);
        $('#linkModal').modal({
            //backdrop: 'static'
        });
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
            QuinenCake.showLoading = false;
        }).on('select2:close', function () {
            QuinenCake.showLoading = true;
        }).maximizeSelect2Height();
    };


    this.updateFromInputValue = function () {
        $('[data-from-input-value]').each(function (i, tag) {
            var fieldName = this.dataset.fromInputValue;
            var input = document.getElementsByName(fieldName);
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
        var placeholder = QuinenCake.getDatePlaceholder(locale);

        $('[data-toggle="datetimepicker"]').each(function () {
            var $source = $(this);
            var $target = $($source.data('date'));

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
                //console.log(event);
            });
        });


    }

    this.startLoading = function () {
        if (QuinenCake.showLoading) {
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

    this.copyToClipboard = function (b) {
        var a = document.createElement("textarea");
        a.value = b;
        a.setAttribute("readonly", "");
        a.style.position = "absolute";
        a.style.left = "-9999px";
        document.body.appendChild(a);
        b = 0 < document.getSelection().rangeCount ? document.getSelection().getRangeAt(0) : !1;
        a.select();
        document.execCommand("copy");
        document.body.removeChild(a);
        b && (document.getSelection().removeAllRanges(), document.getSelection().addRange(b));
    };

    this.initPopover = function () {
        $('[data-toggle="popover"]').popover();

        $('[data-toggle="popover"]').on('shown.bs.popover', function () {
            setTimeout(function () {
                $('[data-toggle="popover"]').popover('hide');
            }, 1000);
        });
    };

    this.listenOnClickTabName = function () {
        $('a[data-toggle]').on('shown.bs.tab', function (event) {
            var name = event.target.dataset['name'];
            $('a[data-name="' + name + '"]').tab('show');
        })
    };

}).apply(QuinenCake);


$(function () {
    QuinenCake.onLoad();
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