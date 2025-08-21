<?php
script('ownshell', 'admin');
?>

<div id="ownshell-admin">
    <form id="ownshell-admin-form" class="section">
        <h2>ownShell Settings</h2>
        <p>
            <label for="allowed_hosts">Allowed Hosts (comma-separated)</label>
            <br>
            <textarea id="allowed_hosts" name="allowed_hosts" cols="50" rows="3"><?php p($_['allowed_hosts']); ?></textarea>
        </p>
        <p>
            <label for="jwt_secret">JWT Secret Key</label>
            <br>
            <input type="text" id="jwt_secret" name="jwt_secret" value="<?php p($_['jwt_secret']); ?>" style="width: 50%;">
            <br>
            <em>This secret must match the secret in your <code>ssh-ws-daemon.php</code> script.</em>
        </p>
        <p>
            <button type="button" id="ownshell-admin-save">Save</button>
            <span id="ownshell-save-indicator" class="hidden"></span>
        </p>
    </form>
</div>
