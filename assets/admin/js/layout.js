$('.flash-message').each(function () {
    const $this = $(this), type = $this.data('flashType'), text = $this.text();

    toastr[type](text);
});

$('.confirm-action').each(function () {
    const $this = $(this);

    $this.click(function (e) {
        if (!$this.data('confirmed')) {
            e.preventDefault();
            Swal.fire($.extend({
                title: 'Are you sure?',
                text: 'You will not be able to recover this!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }, $this.data())).then(function (result) {
                if (result.value) {
                    $this.data('confirmed', true);
                    $this.get(0).click({confirmed: true});
                }
            });
        }
    })
});

$('.row-clickable').each(function () {
    const $this = $(this), url = $this.data('href');

    $this.click(function () {
        if (!window.getSelection().toString()) {
            window.location.href = url;
        }
    });

    $this.mousedown(function (e) {
        const middleClick = 2 === e.which;
        let ctrlClick = false;
        if (1 === e.which && (e.ctrlKey || e.metaKey)) {
            ctrlClick = true;
        }

        if (middleClick || ctrlClick) {
            window.open(url, '_blank');
            e.preventDefault();
        }
    });

});

$('.select2').each(function () {
    const $this = $(this), options = $this.data();
    if (options.ajax) {
        options.ajax.data = function (params) {
            return {
                [options.ajax.searchOn]: params.term,
                page: params.page || 1,
                size: options.ajax.searchLimit
            };
        };
        options.ajax.processResults = function (data) {
            let results = [];
            for (let i = 0; i < data.results.length; i++) {
                const item = data.results[i];
                results.push({id: item[options.ajax.valueField], text: item[options.ajax.displayField]})
            }

            return {
                results,
                pagination: {
                    more: data.has_next
                }
            };
        };
    } else {
        options.matcher = function (params, data) {
            params.term = params.term || '';

            return 0 === data.text.toUpperCase().indexOf(params.term.toUpperCase()) ? data : null;
        };
    }

    $this.select2(options);
});

/**
$(".tags-select").select2({
    // enable tagging
    tags: true,

    // max tags is 3
    maximumSelectionLength: 3,

    // loading remote data
    // see https://select2.github.io/options.html#ajax
    ajax: {
        url: "http://0.0.0.0/admin/tags.json",
        processResults: function (data, page) {
            return {
                results: data
            };
        }
    }
});

 **/

$('.tags-input').each(function () {
    const $this = $(this), options = $this.data();

    $this.tagsinput(options);
});
