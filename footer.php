<?php // get_sidebar(); ?>
</div>
<footer id="footer" role="contentinfo">
    <div id="big_footer">
        <div class="container">
            <div class="row justify-content-around">
                <div class="col-md-4">
                    <?php if (is_active_sidebar('footer-1-widget-area')) : ?>
                        <div id="footer-widget" class="widget-footer-area">
                            <?php dynamic_sidebar('footer-1-widget-area'); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <?php if (is_active_sidebar('footer-2-widget-area')) : ?>
                        <div id="footer-widget" class="widget-footer-area">
                            <?php dynamic_sidebar('footer-2-widget-area'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="copyright">
        <?php if (is_active_sidebar('copyright-footer-widget-area')) : ?>
            <?php dynamic_sidebar('copyright-footer-widget-area'); ?>
        <?php endif; ?>
    </div>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
