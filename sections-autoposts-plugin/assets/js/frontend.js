(function($){
    function SAPPagination(gridElement, itemsPerPage) {
        this.$grid = $(gridElement);
        this.$items = this.$grid.children();
        this.itemsPerPage = itemsPerPage;
        this.currentPage = 1;
        this.totalPages = Math.ceil(this.$items.length / this.itemsPerPage);
        
        this.init();
    }

    SAPPagination.prototype.init = function() {
        if (this.totalPages <= 1) return;

        var paginationId = 'sap-pagination-' + Math.random().toString(36).substr(2, 9);
        var $pagination = $('<div class="sap-pagination" id="' + paginationId + '"></div>');
        this.$grid.after($pagination);
        this.$pagination = $pagination;

        this.renderPagination();
        this.showPage(1);
    };

    SAPPagination.prototype.renderPagination = function() {
        var self = this;
        this.$pagination.empty();

        var $prev = $('<button>&larr; Prev</button>');
        $prev.prop('disabled', this.currentPage === 1);
        $prev.on('click', function(){ self.showPage(self.currentPage - 1); });
        this.$pagination.append($prev);

        for (var i = 1; i <= this.totalPages; i++) {
            var $pageBtn = $('<button>' + i + '</button>');
            if (i === this.currentPage) {
                $pageBtn.addClass('active');
            }
            $pageBtn.data('page', i);
            $pageBtn.on('click', function(){
                self.showPage($(this).data('page'));
            });
            this.$pagination.append($pageBtn);
        }

        var $next = $('<button>Next &rarr;</button>');
        $next.prop('disabled', this.currentPage === this.totalPages);
        $next.on('click', function(){ self.showPage(self.currentPage + 1); });
        this.$pagination.append($next);
    };

    SAPPagination.prototype.showPage = function(page) {
        if (page < 1 || page > this.totalPages) return;
        
        this.currentPage = page;
        this.$items.hide();
        
        var start = (page - 1) * this.itemsPerPage;
        var end = start + this.itemsPerPage;
        this.$items.slice(start, end).show();

        this.renderPagination();
        
        $('html, body').animate({
            scrollTop: this.$grid.offset().top - 100
        }, 400);
    };

    $(document).ready(function(){
        $('.sap-latest-grid[data-items-per-page]').each(function(){
            var perPage = parseInt($(this).data('items-per-page')) || 9;
            new SAPPagination(this, perPage);
        });

        $('.sap-shortcode-input').on('click', function(){
            this.select();
            document.execCommand('copy');
        });
    });
})(jQuery);
