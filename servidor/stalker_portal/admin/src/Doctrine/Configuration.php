<?php

namespace Ministra\Admin\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration as BaseConfiguration;
use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
class Configuration extends \Doctrine\DBAL\Migrations\Configuration\Configuration
{
    private $versionAliases = array();
    public static $ALIASES = array('1-initial_schema.sql' => 1535719937, '2-cities.sql' => 1535719938, '3-storage_table.sql' => 1535719939, '4-tv_archive.sql' => 1535719940, '5-external_storage.sql' => 1535719941, '6-playback_limit.sql' => 1535719942, '7-wowza_tmp_link.sql' => 1535719943, '8-account.sql' => 1535719944, '9-vclub_filter.sql' => 1535719945, '10-wowza_load_balancing.sql' => 1535719946, '11-users.sql' => 1535719947, '12-auth.sql' => 1535719948, '13-stb_type.sql' => 1535719949, '14-stb_banks.sql' => 1535719950, '15-access_tokens.sql' => 1535719951, '16-storages.sql' => 1535719952, '17-access_key.sql' => 1535719953, '18-tv_logos.sql' => 1535719954, '19-fake_archive.sql' => 1535719955, '20-media_favorites.sql' => 1535719956, '21-tv_reminder.sql' => 1535719957, '22-refresh_token.sql' => 1535719958, '23-correct_time.sql' => 1535719959, '24-account_number.sql' => 1535719960, '25-epg_claims.sql' => 1535719961, '26-last_channel.sql' => 1535719962, '27-spdif.sql' => 1535719963, '28-user_comment.sql' => 1535719964, '29-cleanup_db.sql' => 1535719965, '30-zones.sql' => 1535719966, '31-indexes.sql' => 1535719967, '32-services.sql' => 1535719968, '33-text.sql' => 1535719969, '34-archive.sql' => 1535719970, '35-users_created.sql' => 1535719971, '36-rating.sql' => 1535719972, '37-video_rent.sql' => 1535719973, '38-local_pvr.sql' => 1535719974, '39-apache_port.sql' => 1535719975, '40-balancer_monitoring.sql' => 1535719976, '41-itv_modified.sql' => 1535719977, '42-ad_weight.sql' => 1535719978, '43-ad_filter.sql' => 1535719979, '44-storage_ua_filter.sql' => 1535719980, '45-user_country.sql' => 1535719981, '46-user_access_token.sql' => 1535719982, '47-epg_desc_text.sql' => 1535719983, '48-vclub_quality.sql' => 1535719984, '49-user_log_time.sql' => 1535719985, '50-player_tv_archive.sql' => 1535719986, '51-vclub_comments.sql' => 1535719987, '52-access_control.sql' => 1535719988, '53-admin_groups_charset.sql' => 1535719989, '54-played_timeshift.sql' => 1535719990, '55-user_plasma_saving.sql' => 1535719991, '56-epg_extended.sql' => 1535719992, '57-epg_status.sql' => 1535719993, '58-device_id.sql' => 1535719994, '59-time_shift.sql' => 1535719995, '60-tv_stat_locale.sql' => 1535719996, '61-nginx_secure_link.sql' => 1535719997, '62-last_id_index.sql' => 1535719998, '63-video_clock.sql' => 1535719999, '64-video_low_quality.sql' => 1535720000, '65-played_video_index.sql' => 1535720001, '66-device_id2.sql' => 1535720002, '67-verified.sql' => 1535720003, '68-archive_duration.sql' => 1535720004, '69-tv_aspect.sql' => 1535720005, '70-hdmi_event_reaction.sql' => 1535720006, '71-lang_priority.sql' => 1535720007, '72-portal_prefs.sql' => 1535720008, '73-imageupdate_prefix.sql' => 1535720009, '74-dvb_channels.sql' => 1535720010, '75-dvb_reminder.sql' => 1535720011, '76-hw_version.sql' => 1535720012, '77-vclub_country.sql' => 1535720013, '78-epg_time.sql' => 1535720014, '79-flussonic_tmp_link.sql' => 1535720015, '80-vclub_genres.sql' => 1535720016, '81-verified.sql' => 1535720017, '82-user_log_param.sql' => 1535720018, '83-openweathermap.sql' => 1535720019, '84-portal_settings.sql' => 1535720020, '85_package_price.sql' => 1535720021, '86_audioclub.sql' => 1535720022, '87_playlists.sql' => 1535720023, '88_flussonic_dvr.sql' => 1535720024, '89-user_theme.sql' => 1535720025, '90-vclub_wowza.sql' => 1535720026, '91-wowza_dvr.sql' => 1535720027, '92-add_settings_pass_in_users.sql' => 1535720028, '93-event_param.sql' => 1535720029, '94-add_fav_radio_table.sql' => 1535720030, '95-admin_grp_perm.sql' => 1535720031, '96-adm_dropdown_attributes_and_tvchannels_lock.sql' => 1535720032, '97-package_subscribe_log.sql' => 1535720033, '98-update_tv_genres_for_edit.sql' => 1535720034, '99-expire_billing_date.sql' => 1535720035, '100-reseller_table.sql' => 1535720036, '101-epg_lang_order.sql' => 1535720037, '102-radio_monitoring.sql' => 1535720038, '103-account_balance.sql' => 1535720039, '104-update_adm_grp_action_access.sql' => 1535720040, '105-update_for_statistics_page.sql' => 1535720041, '106-max_users_for_reseller.sql' => 1535720042, '107-download_links.sql' => 1535720043, '108-update_video_cat_genres_for_edit.sql' => 1535720044, '109-filters.sql' => 1535720045, '110-add_post_func_in_events.sql' => 1535720046, '111-update_ru_country_names.sql' => 1535720047, '112-external_apps.sql' => 1535720048, '113-fix_filters_action.sql' => 1535720049, '114-app_alias.sql' => 1535720050, '115-app_name_and_github_cache.sql' => 1535720051, '116-app_autoupdate.sql' => 1535720052, '117-application_catalog_pages.sql' => 1535720053, '118-default_video_out.sql' => 1535720054, '119-schedule_events.sql' => 1535720055, '120-update_for_filters.sql' => 1535720056, '121-app_description.sql' => 1535720057, '122-update_for_users_activity.sql' => 1535720058, '123-update_application_tos.sql' => 1535720059, '124-app_icons.sql' => 1535720060, '125-add_media_type_filter.sql' => 1535720061, '126-users_index.sql' => 1535720062, '127-new_app.sql' => 1535720063, '128-admin_module_update.sql' => 1535720064, '129-fill_empty_logins.sql' => 1535720065, '130-update_action_access.sql' => 1535720066, '131-m3u_import_ations.sql' => 1535720067, '132-audio_genre.sql' => 1535720068, '133-user_log_index.sql' => 1535720069, '134-move_admin_to_group_action.sql' => 1535720070, '135-check_epg_prefix_action.sql' => 1535720071, '136-update_admin_permissions_title.sql' => 1535720072, '137-apps_localization.sql' => 1535720073, '138-reset_mediaclaims_action.sql' => 1535720074, '139-strain_field_monutoring_url.sql' => 1535720075, '140-user_client_type.sql' => 1535720076, '141-user_hash.sql' => 1535720077, '142-stalker_updates.sql' => 1535720078, '143-tariff_plan_control_action.sql' => 1535720079, '144-new_videoclub_structure.sql' => 1535720080, '145-update_new_videoclub_tables.sql' => 1535720081, '146_time_delta.sql' => 1535720082, '147-api_storage.sql' => 1535720083, '148-xtreamcodes_support.sql' => 1535720084, '149-censored_tv_video_categories.sql' => 1535720085, '150-languages.sql' => 1535720086, '151-vclub_not_ended.sql' => 1535720087, '152-migrate_videoclub_users_permitions.sql' => 1535720088, '153-add_quality_table.sql' => 1535720089, '154-add_video_provider.sql' => 1535720090, '155-apps_config.sql' => 1535720091, '156-add_moderator_tasks_index.sql' => 1535720092, '157-add_block_for_admin_access_module.sql' => 1535720093, '158-tv_channels_history.sql' => 1535720094, '159-playback_sessions.sql' => 1535720095, '160-add_iso_639_3_codes.sql' => 1535720096, '161-launcher_apps.sql' => 1535720097, '162-migrate_applications_users_permitions.sql' => 1535720098, '163-launcher_apps_options.sql' => 1535720099, '164-add_admin_permitions_data.sql' => 1535720100, '165-launcher_apps_manual_install.sql' => 1535720101, '166-launcher_apps_tos.sql' => 1535720102, '167-add_backupapps_permitions.sql' => 1535720103, '168-launcher_apps_available_version.sql' => 1535720104, '169-add_updateapps_permitions.sql' => 1535720105, '170-video_last_played.sql' => 1535720106, '171-access_token_refresh_time.sql' => 1535720107, '172-add_certificates_permitions.sql' => 1535720108, '173-user_units.sql' => 1535720109, '174-group_autoupdate.sql' => 1535720110, '175-add_actions_permitions.sql' => 1535720111, '176-vod_tmp_link.sql' => 1535720112, '177-add_indexes.sql' => 1535720113, '178-external_advertise.sql' => 1535720114, '179-add_opinion_flag.sql' => 1535720115, '180-add_duration_audio_composition.sql' => 1535720116, '181-add_fav_karaoke_table.sql' => 1535720117, '182-edgecast_auth_support.sql' => 1535720118, '183-nimble_support.sql' => 1535720119, '184-akamai_auth_support.sql' => 1535720120, '185-external_ad_update.sql' => 1535720121, '186-mtr.sql' => 1535720122, '187-subtitle_color.sql' => 1535720123, '188-notification_feed.sql' => 1535720124, '189-stb_played_video.sql' => 1535720125, '190-last_id_uid_index.sql' => 1535720126, '191-tech_support.sql' => 1535720127, '192-ad_times.sql' => 1535720128, '193-update_adm_grp_action_access.sql' => 1535720129, '194-add_admin_lang.sql' => 1535720130, '195-add_schedule_events_channel.sql' => 1535720131, '196-external_adv_add_position.sql' => 1535720132, '197-watched_settings.sql' => 1535720133, '198-add_admin_feed_action_access.sql' => 1535720134, '199-add_admin_viewed_settings_action_access.sql' => 1535720135, '200-not_ended_movie.sql' => 1535720136, '201-ad_skip.sql' => 1535720137, '202-add_admin_new_videoclub_action_access.sql' => 1535720138, '203-ip_range_for_stream_zones.sql' => 1535720139, '204-service_in_package_options.sql' => 1535720140, '205-fix_admin_new_videoclub_action_access.sql' => 1535720141, '206-add_itv_index.sql' => 1535720142, '207-admin_theme_support.sql' => 1535720143, '208-tarifs_notification.sql' => 1535720144, '209-language_fix.sql' => 1535720145, '210-itv_language.sql' => 1535720146, '211-add_admin_itv_action_access.sql' => 1535720147, '212-add_admin_itv_action_access_fix.sql' => 1535720148, '213-tvarchives_typs.sql' => 1535720149, '214-smac.sql' => 1535720150, '215-smac_user_id.sql' => 1535720151, '216-extend_tvarchives_typs.sql' => 1535720152, '217-fix_tvarchives_stalkerdvr.sql' => 1535720153, '218-fix_admin_action_perms.sql' => 1535720154, '219-add_admin_activationcodes_action_access.sql' => 1535720155, '220-wowza_securetoken.sql' => 1535720156, '221-storages_dvr_types.sql' => 1535720157, '222-fix_storages_stalkerdvr.sql' => 1535720158, '223-add_admin_resetvideoclaims_action_access.sql' => 1535720159, '224-fix_admin_action_access.sql' => 1535720160, '225-activation_codes.sql' => 1535720161, '226-stb_activation_code.sql' => 1535720162, '227-add_admin_activationcodes_action_access.sql' => 1535720163, '228-fix_admin_action_access.sql' => 1535720164, '229-smac_manual_status.sql' => 1535720165, '230-themes.sql' => 1535720166, '231-default_launcher_theme.sql' => 1535720167, '232-series_number.sql' => 1535720168, '233-add_theme_managment_action_access.sql' => 1535720169, '234-add_admin_servicepackage_action_access.sql' => 1535720170, '235-update_adm_grp_action_access_description.sql' => 1535720171, '236-update_tos.sql' => 1535720172, '237-user_settings_time_format.sql' => 1535720173, '238-update_user_filter_title.sql' => 1535720174, '239-update_tos_vertamedia.sql' => 1535720175, '240-fixflussonic_channels.sql' => 1535720176, '241-rename_to_license_keys.sql' => 1535720177, '242-delete_duplicate_filter_definition.sql' => 1535720178, '243-add_video_indexes.sql' => 1535720179, '244-videoclub_imdb_rating_fix.sql' => 1535720180, '245-flex_cdn.sql' => 1535720181, '246-change_videoclub_rating_type.sql' => 1535720182, '247-add_smac_repaire_date.sql' => 1535720183, '248-course_refactor.sql' => 1535720184, '249-karaoke_nfs_to_http_migrate.sql' => 1535720185, '250-notification_feed_add_deleted_field.sql' => 1535720186, '251-admin_id-to-video.sql' => 1535720187, '251-ip_range_for_resellers.sql' => 1535720188, '252-ip_range_for_resellers.sql' => 1535720188, '252-admin_access_rules_for_resellers_actions.sql' => 1535720189, '253-admin_access_rules_for_resellers_actions.sql' => 1535720189, '254_add_permissions_for_tariff-plan.sql' => 1535720190, '255_update_index_routes.sql' => 1535720191, '256_users_group_operations_perm.sql' => 1535720192, '257_update_season_series_fields.sql' => 1535720193, '258_add_routes_for_itv_checking.sql' => 1535720194, '259_add_routes_for_tariffs_checking.sql' => 1535720195, '260-update_adm_grp_action_access_ajax_status.sql' => 1535720196);
    private $migrationTableCreated = false;
    protected $migrations;
    public function __construct(\Doctrine\DBAL\Connection $connection, \Doctrine\DBAL\Migrations\OutputWriter $outputWriter = null, \Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface $finder = null)
    {
        parent::__construct($connection, $outputWriter, $finder);
        foreach (self::$ALIASES as $alias => $time) {
            $this->versionAliases[$time] = $alias;
        }
    }
    public function createMigrationTable()
    {
        $this->validate();
        if ($this->migrationTableCreated) {
            return false;
        }
        $this->connect();
        $filenameColumn = new \Doctrine\DBAL\Schema\Column('origin_filename', \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::STRING), ['length' => 500, 'notNull' => true]);
        $mColumn = new \Doctrine\DBAL\Schema\Column($this->getMigrationsColumnName(), \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::STRING), ['length' => 255]);
        $mDescription = new \Doctrine\DBAL\Schema\Column('migration_description', \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::STRING), ['length' => 255]);
        if ($this->getConnection()->getSchemaManager()->tablesExist([$this->getMigrationsTableName()])) {
            $this->migrationTableCreated = true;
            $columns = \array_keys($this->getConnection()->getSchemaManager()->listTableColumns($this->getMigrationsTableName()));
            if (!\in_array('origin_filename', $columns)) {
                $diff = new \Doctrine\DBAL\Schema\TableDiff($this->getMigrationsTableName(), ['origin_filename' => $filenameColumn]);
                $this->getConnection()->getSchemaManager()->alterTable($diff);
            }
            if (!\in_array('migration_description', $columns)) {
                $diff = new \Doctrine\DBAL\Schema\TableDiff($this->getMigrationsTableName(), ['migration_description' => $mDescription]);
                $this->getConnection()->getSchemaManager()->alterTable($diff);
            }
            if (!\in_array($this->getMigrationsColumnName(), $columns)) {
                $diff = new \Doctrine\DBAL\Schema\TableDiff($this->getMigrationsTableName(), [$this->getMigrationsColumnName() => $mColumn]);
                $this->getConnection()->getSchemaManager()->alterTable($diff);
            }
            return false;
        }
        $columns = ['change_number' => new \Doctrine\DBAL\Schema\Column('change_number', \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::BIGINT), ['notNull' => true, 'autoIncrement' => true]), $this->getMigrationsColumnName() => $mColumn, 'delta_set' => new \Doctrine\DBAL\Schema\Column('delta_set', \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::STRING), ['notNull' => true]), 'start_dt' => new \Doctrine\DBAL\Schema\Column('start_dt', \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::DATETIME), ['notNull' => true]), 'applied_by' => new \Doctrine\DBAL\Schema\Column('applied_by', \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::DATETIME), ['length' => 255]), 'description' => new \Doctrine\DBAL\Schema\Column('description', \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::STRING), ['length' => 255]), 'complete_dt' => new \Doctrine\DBAL\Schema\Column('complete_dt', \Doctrine\DBAL\Types\Type::getType(\Doctrine\DBAL\Types\Type::DATETIME), ['length' => 255]), $filenameColumn->getName() => $filenameColumn, 'migration_description' => $mDescription];
        $table = new \Doctrine\DBAL\Schema\Table($this->getMigrationsTableName(), $columns);
        $table->setPrimaryKey(['change_number']);
        $table->addUniqueIndex([$this->getMigrationsColumnName()], 'uniq_version');
        $this->getConnection()->getSchemaManager()->createTable($table);
        $this->migrationTableCreated = true;
        return true;
    }
    public function findDescriptionByTime($time)
    {
        return isset($this->versionAliases[$time]) ? $this->versionAliases[$time] : false;
    }
    public function registerMigration($version, $class)
    {
        parent::registerMigration($version, $class);
        $version = new \Ministra\Admin\Doctrine\MigrationVersion($this, $version, $class);
        $this->migrations[$version->getVersion()] = $version;
        \ksort($this->migrations, SORT_STRING);
        return $version;
    }
    public function getMigrations()
    {
        return $this->migrations;
    }
    public function getMigrationsToExecute($direction, $to)
    {
        if (empty($this->migrations)) {
            $this->registerMigrationsFromDirectory($this->getMigrationsDirectory());
        }
        if ($direction === \Doctrine\DBAL\Migrations\Version::DIRECTION_DOWN) {
            if (\count($this->migrations)) {
                $allVersions = \array_reverse(\array_keys($this->migrations));
                $classes = \array_reverse(\array_values($this->migrations));
                $allVersions = \array_combine($allVersions, $classes);
            } else {
                $allVersions = [];
            }
        } else {
            $allVersions = $this->migrations;
        }
        $versions = [];
        $migrated = $this->getMigratedVersions();
        foreach ($allVersions as $version) {
            if ($this->shouldExecuteMigration($direction, $version, $to, $migrated)) {
                $versions[$version->getVersion()] = $version;
            }
        }
        return $versions;
    }
    private function shouldExecuteMigration($direction, \Doctrine\DBAL\Migrations\Version $version, $to, $migrated)
    {
        if ($direction === \Doctrine\DBAL\Migrations\Version::DIRECTION_DOWN) {
            if (!\in_array($version->getVersion(), $migrated)) {
                return false;
            }
            return $version->getVersion() > $to;
        }
        if ($direction === \Doctrine\DBAL\Migrations\Version::DIRECTION_UP) {
            if (\in_array($version->getVersion(), $migrated)) {
                return false;
            }
            return $version->getVersion() <= $to;
        }
    }
}
