<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('Area of responsibility', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-xs-12">
                <article>
                    <p><?php _e('Please add your work assignments. This will make it easier to find you when searching.', 'municipio-intranet'); ?></p>
                    <p><?php _e('Example work assignments', 'municipio-intranet'); ?>:</p>
                    <ul>
                        <li><?php _e('Intranet web developer', 'municipio-intranet'); ?></li>
                        <li><?php _e('Office management', 'municipio-intranet'); ?></li>
                        <li><?php _e('Human resource', 'municipio-intranet'); ?></li>
                    </ul>
                </article>
            </div>
        </div>

        <div class="grid">
            <div class="grid-xs-12">
                <div class="form-group">
                    <label for="responsibility-autocomplete"><?php _e('Responsibility', 'municipio-intranet'); ?></label>
                    <div class="tag-manager" data-input-name="user_responsibilities" data-wp-ajax-action="autocomplete_responsibilities">
                        <div class="tag-manager-input">
                            <div class="input-group">
                                <input type="text" name="tag" class="form-control" placeholder="<?php _e('Add area of responsibility', 'municipio-intranet'); ?>…" autocomplete="off">
                                <span class="input-group-addon-btn"><button name="add-tag" class="btn"><?php _e('Add', 'municipio-intranet'); ?></button></span>
                            </div>
                        </div>
                        <div class="tag-manager-selected">
                            <label class="label-sm"><?php _e('Added responsibilities', 'municipio-intranet'); ?></label>
                            <ul class="tags">
                                @if (get_the_author_meta('user_responsibilities', get_current_user_id()))
                                @foreach (get_the_author_meta('user_responsibilities', get_current_user_id()) as $item)
                                <li>
                                    <button class="btn btn-plain" data-action="remove">&times;</button>
                                    {{ $item }}
                                    <input type="hidden" name="user_responsibilities[]" value="{{ $item }}">
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
