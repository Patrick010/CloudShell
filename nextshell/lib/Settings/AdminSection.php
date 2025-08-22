<?php
namespace OCA\nextshell\Settings;

use OCP\IURLGenerator;
use OCP\Settings\IIconSection;
use OCP\IL10N;

class AdminSection implements IIconSection {

    private IL10N $l;
    private IURLGenerator $urlGenerator;

    public function __construct(IL10N $l, IURLGenerator $urlGenerator) {
        $this->l = $l;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * returns the ID of the section. It is supposed to be a lower case string
     */
    public function getID(): string {
        return 'nextshell';
    }

    /**
     * returns the translated name as it should be displayed in the navigation
     */
    public function getName(): string {
        return $this->l->t('NextShell');
    }

    /**
     * returns the relative path to the SVG icon
     */
    public function getIcon(): string {
        return $this->urlGenerator->imagePath('nextshell', 'app.svg');
    }
}
