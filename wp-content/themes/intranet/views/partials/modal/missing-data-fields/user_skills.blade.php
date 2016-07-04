<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('Skills', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-md-12">
                <p><?php _e('Please add your skills. This will make it easier to find you when searching.', 'municipio-intranet'); ?></p>
            </div>
        </div>

       <div class="grid">
            <div class="grid-md-12">
                <div class="form-group">
                    <label for="responsibility-autocomplete"><?php _e('Skill', 'municipio-intranet'); ?></label>
                    <div class="tag-manager" data-input-name="user_skills" data-wp-ajax-action="autocomplete_skills">
                        <div class="tag-manager-input">
                            <div class="input-group">
                                <input type="text" name="tag" class="form-control" placeholder="<?php _e('Add skill', 'municipio-intranet'); ?>…" autocomplete="off">
                                <span class="input-group-addon-btn"><button name="add-tag" class="btn"><?php _e('Add', 'municipio-intranet'); ?></button></span>
                            </div>
                        </div>
                        <div class="tag-manager-selected">
                            <label class="label-sm"><?php _e('Added skills', 'municipio-intranet'); ?></label>
                            <ul class="tags">
                                @if (get_the_author_meta('user_skills', get_current_user_id()))
                                @foreach (get_the_author_meta('user_skills', get_current_user_id()) as $item)
                                <li>
                                    <button class="btn btn-plain" data-action="remove">&times;</button>
                                    {{ $item }}
                                    <input type="hidden" name="user_skills[]" value="{{ $item }}">
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
