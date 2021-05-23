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
