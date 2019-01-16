<?php
    namespace nikserg\sbis;

    class SbisClient
    {
        /**
         * @var string[]
         */
        public $phones;
        /**
         * @var string[]
         */
        public $emails;
        /**
         * @var string[]
         */
        public $sites;

        public function getPhonesString() {

            if (empty($this->phones)) {
                return '';
            }
            return implode(', ', $this->phones);
        }
        public function getEmailsString() {
            if (empty($this->emails)) {
                return '';
            }
            return implode(', ', $this->emails);
        }
        public function getSitesString() {
            if (empty($this->sites)) {
                return '';
            }
            return implode(', ', $this->sites);
        }

    }