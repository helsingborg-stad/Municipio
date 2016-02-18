<?php echo $before_widget; ?>
<?php foreach ($contacts as $contact) : ?>
<ul>
    <?php if (isset($contact['contact_person']) && !empty($contact['contact_person'])) : ?>
        <li class="contanct-person"><?php echo $contact['contact_person']; ?></li>
    <?php endif; ?>

    <?php if (isset($contact['contact_company']) && !empty($contact['contact_company'])) : ?>
        <li class="contanct-company"><?php echo $contact['contact_company']; ?></li>
    <?php endif; ?>

    <?php if (isset($contact['phone_numer']) && !empty($contact['phone_numer'])) : ?>
        <li class="contanct-phone"><?php echo $contact['phone_numer']; ?></li>
    <?php endif; ?>

    <?php if (isset($contact['email']) && !empty($contact['email'])) : ?>
        <li class="contanct-email"><?php echo $contact['email']; ?></li>
    <?php endif; ?>

    <?php if (isset($contact['address']) && !empty($contact['address'])) : ?>
        <li class="contanct-address"><?php echo $contact['address']; ?></li>
    <?php endif; ?>
</ul>
<?php endforeach; ?>
<?php echo $after_widget; ?>
