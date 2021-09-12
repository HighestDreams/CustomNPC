<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC;

class Language
{
    /* Rca command's message */
    public const RCA_PLAYER_NOTFOUND = 0;
    /* NPC messages */
    public const NPC_TELEPORT = 5;
    public const NPC_EDIT_ENABLED = 1;
    public const NPC_CREATION_MESSAGE = 4;
    public const NPC_NEVER_ADDED_COMMAND = 3;

    public const NPC_EDITMODE_DISABLE = 2;
    public const NPC_TELEPORT_TUTORIAL = 6;
    public const NPC_DELETATION = 7;
    public const NPC_DELETE_SURE = 8;
    public const COMMAND_BELOW = 9;
    public const CHANGES = 10;
    public const ROTATION = 11;
    public const COOLDOWN = 12;
    public const EMOTE = 13;
    public const SKIN = 13;
    public static $NPC;

    /**
     * @param int $text
     * @return string|void
     */
    public static function translated (int $text)
    {
        $language = strtolower(NPC::$settings->get('language'));
        if (is_null($language) or !in_array($language, ['en', 'ru', 'ge', 'ch', 'ko', 'je', 'fr'])) {
            $language = 'en'; /* Default language */
        }

        switch ($text) {
            case self::RCA_PLAYER_NOTFOUND:
                switch ($language) {
                    case 'en':
                        return 'Player not found.';

                    case 'ru':
                        return 'Игрок не найден.';

                    case 'ge':
                        return 'Spieler nicht gefunden.';

                    case 'ch':
                        return '找不到播放器.';

                    case 'je':
                        return 'プレイヤーが見つかりません.';

                    case 'ko':
                        return '플레이어를 찾을 수 없습니다.';

                    case 'fr':
                        return 'Joueur non trouvé.';
                }
                break;

            case self::NPC_EDIT_ENABLED:
                switch (strtolower($language)) {
                    case 'en':
                        return 'NPC editing mode enabled for you! Tap any NPC you want to edit, use (/npc edit) command to disable NPC edit mode';

                    case 'ru':
                        return 'Для вас включен режим редактирования NPC! Коснитесь любого NPC, которого хотите отредактировать, используйте команду (/npc edit), чтобы отключить режим редактирования NPC.';

                    case 'ge':
                        return 'NPC-Bearbeitungsmodus für Sie aktiviert! Tippen Sie auf einen NPC, den Sie bearbeiten möchten, verwenden Sie den Befehl (/npc edit), um den NPC-Bearbeitungsmodus zu deaktivieren';

                    case 'ch':
                        return '为您启用NPC编辑模式！点击任何你想编辑的 NPC，使用 (/npc edit) 命令禁用 NPC 编辑模式';

                    case 'je':
                        return 'NPC編集モードが有効になっています！編集するNPCをタップし、（/npc edit）コマンドを使用してNPC編集モードを無効にします';

                    case 'ko':
                        return 'NPC 편집 모드가 활성화되었습니다! 편집하려는 NPC를 누르고 (/npc edit) 명령을 사용하여 NPC 편집 모드를 비활성화합니다.';

                    case 'fr':
                        return "Mode d'édition NPC activé pour vous ! Appuyez sur n'importe quel PNJ que vous souhaitez modifier, utilisez la commande (/npc edit) pour désactiver le mode d'édition NPC";
                }
                break;

            case self::NPC_EDITMODE_DISABLE:
                switch (strtolower($language)) {
                    case 'en':
                        return 'Editing mode disabled for you.';

                    case 'ru':
                        return 'Режим редактирования отключен для вас.';

                    case 'ge':
                        return 'Bearbeitungsmodus für Sie deaktiviert.';

                    case 'ch':
                        return '已为您禁用编辑模式。';

                    case 'je':
                        return '編集モードが無効になっています。';

                    case 'ko':
                        return '편집 모드가 비활성화되었습니다.';

                    case 'fr':
                        return 'Mode édition désactivé pour vous.';
                }
                break;

            case self::NPC_NEVER_ADDED_COMMAND:
                switch (strtolower($language)) {
                    case 'en':
                        return 'For enable NPC editing mode use : /npc edit';

                    case 'ru':
                        return 'Для включения режима редактирования NPC используйте: /npc edit';

                    case 'ge':
                        return 'Um den NPC-Bearbeitungsmodus zu aktivieren, verwenden Sie: /npc edit';

                    case 'ch':
                        return '要启用 NPC 编辑模式，请使用：/npc edit';

                    case 'je':
                        return 'NPC編集モードを有効にするには：/npc edit';

                    case 'ko':
                        return 'NPC 편집 모드를 활성화하려면 다음을 사용하십시오. /npc edit';

                    case 'fr':
                        return "Pour activer le mode d'édition NPC, utilisez : /npc edit";
                }
                break;

            case self::NPC_CREATION_MESSAGE:
                switch (strtolower($language)) {
                    case 'en':
                        return 'NPC created successfully! For customizing you need first enable npc editor mode! for this, use command /npc edit';

                    case 'ru':
                        return 'NPC успешно создан! Для настройки вам необходимо сначала включить режим редактора npc! для этого используйте команду /npc edit';

                    case 'ge':
                        return 'NPC erfolgreich erstellt! Zum Anpassen müssen Sie zuerst den npc-Editor-Modus aktivieren! Verwenden Sie dazu den Befehl /npc edit';

                    case 'ch':
                        return 'NPC创建成功！要进行自定义，您需要先启用 npc 编辑器模式！为此，请使用命令 /npc edit';

                    case 'je':
                        return 'NPCが正常に作成されました！カスタマイズするには、最初にnpcエディターモードを有効にする必要があります！これには、コマンド/npc editを使用します';

                    case 'ko':
                        return 'NPC 생성 성공! 커스터마이징을 위해서는 먼저 npc 편집기 모드를 활성화해야 합니다! 이를 위해 /npc edit 명령을 사용하십시오';

                    case 'fr':
                        return "PNJ créé avec succès ! Pour personnaliser, vous devez d'abord activer le mode éditeur npc ! pour cela, utilisez la commande /npc edit";
                }
                break;

            case self::NPC_TELEPORT:
                switch (strtolower($language)) {
                    case 'en':
                        return ' has been teleported to you successfully.';

                    case 'ru':
                        return ' был успешно телепортирован к вам.';

                    case 'ge':
                        return ' wurde erfolgreich zu dir teleportiert.';

                    case 'ch':
                        return ' 已成功传送给您。';

                    case 'je':
                        return ' 正常にテレポートされました。';

                    case 'ko':
                        return ' 성공적으로 텔레포트되었습니다.';

                    case 'fr':
                        return ' vous a été téléporté avec succès.';
                }
                break;

            case self::NPC_TELEPORT_TUTORIAL:
                switch (strtolower($language)) {
                    case 'en':
                        return 'Go to the place you want and then send §chere §ain the chat.';

                    case 'ru':
                        return 'Перейдите в нужное место и отправьте в чат §chere.';

                    case 'ge':
                        return 'Gehen Sie an den gewünschten Ort und senden Sie dann §chere §in den Chat.';

                    case 'ch':
                        return '转到您想要的地方，然后发送 §chere §a 聊天。';

                    case 'je':
                        return '目的の場所に移動し、チャットで§chere§aを送信します。';

                    case 'ko':
                        return '원하는 장소로 이동한 다음 채팅에서 §chere §보내십시오.';

                    case 'fr':
                        return "Allez à l'endroit que vous voulez puis envoyez §chere §ain le chat.";
                }
                break;

            case self::NPC_DELETATION:
                switch (strtolower($language)) {
                    case 'en':
                        return 'NPC deleted successfully.';

                    case 'ru':
                        return 'NPC успешно удален.';

                    case 'ge':
                        return 'NPC erfolgreich gelöscht.';

                    case 'ch':
                        return 'NPC删除成功。';

                    case 'je':
                        return 'NPCが正常に削除されました。';

                    case 'ko':
                        return 'NPC가 성공적으로 삭제되었습니다.';

                    case 'fr':
                        return 'NPC supprimé avec succès.';
                }
                break;

            case self::NPC_DELETE_SURE:
                switch (strtolower($language)) {
                    case 'en':
                        return 'will be deleted, are you sure for this action?';

                    case 'ru':
                        return 'будет удален, вы уверены в этом действии?';

                    case 'ge':
                        return 'wird gelöscht. Sind Sie für diese Aktion sicher?';

                    case 'ch':
                        return '将被删除，您确定要执行此操作吗？';

                    case 'je':
                        return '削除されますが、このアクションを実行してもよろしいですか？';

                    case 'ko':
                        return '삭제됩니다. 이 작업을 수행하시겠습니까?';

                    case 'fr':
                        return 'sera supprimé, êtes-vous sûr de cette action ?';
                }
                break;

            case self::COMMAND_BELOW:
                switch (strtolower($language)) {
                    case 'en':
                        return 'Write your new command in the input below.';

                    case 'ru':
                        return 'Напишите вашу новую команду во входных данных ниже.';

                    case 'ge':
                        return 'Schreiben Sie Ihren neuen Befehl in die Eingabe unten.';

                    case 'ch':
                        return '在下面的输入中写入新命令。';

                    case 'je':
                        return '以下の入力に新しいコマンドを記述します。';

                    case 'ko':
                        return '아래 입력에 새 명령을 작성하십시오.';

                    case 'fr':
                        return "Écrivez votre nouvelle commande dans l'entrée ci-dessous.";
                }
                break;

            case self::CHANGES:
                switch (strtolower($language)) {
                    case 'en':
                        return 'NPC changes saved.';

                    case 'ru':
                        return 'Изменения NPC сохранены.';

                    case 'ge':
                        return 'NPC-Änderungen gespeichert.';

                    case 'ch':
                        return 'NPC 更改已保存。';

                    case 'je':
                        return 'NPCの変更が​​保存されました。';

                    case 'ko':
                        return 'NPC 변경 사항이 저장되었습니다.';

                    case 'fr':
                        return 'Modifications du PNJ enregistrées.';
                }
                break;

            case self::ROTATION:
                switch (strtolower($language)) {
                    case 'en':
                        return 'Enable/Disable Rotation of NPC';

                    case 'ru':
                        return 'Включить/отключить вращения NPC';

                    case 'ge':
                        return 'Aktivieren/Deaktivieren der NPC-Rotation';

                    case 'ch':
                        return '启用/禁用 NPC 的旋转';

                    case 'je':
                        return 'NPCの回転を有効/無効にする';

                    case 'ko':
                        return 'NPC 회전 활성화/비활성화';

                    case 'fr':
                        return 'Activer/Désactiver la rotation des PNJ';
                }
                break;

            case self::COOLDOWN:
                switch (strtolower($language)) {
                    case 'en':
                        return 'Cool Down per second to prevent NPC click spam by players! (To disable the following input, set it to 0!)';

                    case 'ru':
                        return 'Задержка в секундах, чтобы предотвратить спам NPC со стороны игроков! (Чтобы отключить следующий вход, установите его на 0!)';

                    case 'ge':
                        return 'Cool Down pro Sekunde, um NPC-Klick-Spam durch Spieler zu verhindern! (Um den folgenden Eingang zu deaktivieren, setzen Sie ihn auf 0!)';

                    case 'ch':
                        return '每秒冷却，以防止玩家点击垃圾邮件！ （要禁用以下输入，请将其设置为 0！）';

                    case 'je':
                        return 'プレイヤーによるNPCクリックスパムを防ぐために毎秒クールダウン！ （次の入力を無効にするには、0に設定してください！）';

                    case 'ko':
                        return '플레이어의 NPC 클릭 스팸을 방지하기 위해 초당 쿨다운! (다음 입력을 비활성화하려면 0으로 설정하십시오!)';

                    case 'fr':
                        return "Refroidissement par seconde pour empêcher le spam de clics sur les PNJ par les joueurs ! (Pour désactiver l'entrée suivante, réglez-la sur 0 !)";
                }
                break;

            case self::SKIN:
                switch (strtolower($language)) {
                    case 'en':
                        return "Change the skin of NPC.\n§3+ §6You can change NPC skin to any online player in server! Choose a player from dropdown below to change NPC skin! (Attention: If you dont want to change the skin of NPC then DO NOT choose a player!)";

                    case 'ru':
                        return "Измените скин NPC. \n§3 + §6Вы можете изменить скин NPC на любого онлайн-игрока на сервере! Выберите игрока из раскрывающегося списка ниже, чтобы сменить скин NPC! (Внимание: если вы не хотите менять скин NPC, НЕ выбирайте игрока!)";

                    case 'ge':
                        return "Ändere den Skin des NPCs.\n§3+ §6Du kannst den NPC-Skin zu jedem Online-Spieler auf dem Server ändern! Wähle einen Spieler aus der Dropdown-Liste unten, um den NPC-Skin zu ändern! (Achtung: Wenn Sie den Skin von NPC nicht ändern möchten, wählen Sie KEINEN Spieler!)";

                    case 'ch':
                        return "更换NPC皮肤。\n§3+§6您可以为服务器中的任何在线玩家更换NPC皮肤！从下面的下拉列表中选择一个玩家来更改 NPC 皮肤！ （注意：如果你不想改变NPC的皮肤，那就不要选择玩家！）";

                    case 'je':
                        return "NPCのスキンを変更します。\n§3+§6サーバー内の任意のオンラインプレーヤーにNPCスキンを変更できます。下のドロップダウンからプレイヤーを選択して、NPCスキンを変更してください！ （注意：NPCのスキンを変更したくない場合は、プレイヤーを選択しないでください！）";

                    case 'ko':
                        return "NPC의 스킨을 변경하세요.\n§3+ §6NPC 스킨을 서버의 온라인 플레이어로 변경할 수 있습니다! 아래 드롭다운에서 플레이어를 선택하여 NPC 스킨을 변경하세요! (주의: NPC의 스킨을 변경하고 싶지 않다면 플레이어를 선택하지 마십시오!)";

                    case 'fr':
                        return "Changez l'apparence du PNJ.\n§3+ §6Vous pouvez changer l'apparence du PNJ pour n'importe quel joueur en ligne sur le serveur ! Choisissez un joueur dans la liste déroulante ci-dessous pour changer de skin de PNJ ! (Attention : si vous ne voulez pas changer le skin du PNJ, NE choisissez PAS de joueur !)";
                }
                break;

            case self::EMOTE:
                switch (strtolower($language)) {
                    case 'en':
                        return 'Select an emote for NPC (Emotes timer can be change in Settings.yml!)';

                    case 'ru':
                        return 'Выберите эмоцию для NPC (таймер эмоций можно изменить в Settings.yml!)';

                    case 'ge':
                        return 'Wählen Sie ein Emote für NPC (Emotes-Timer kann in Settings.yml geändert werden!)';

                    case 'ch':
                        return '为 NPC 选择一个表情（表情计时器可以在 Settings.yml 中更改！）';

                    case 'je':
                        return 'NPCのエモートを選択します（エモートタイマーはSettings.ymlで変更できます！）';

                    case 'ko':
                        return 'NPC에 대한 이모티콘 선택(이모트 타이머는 Settings.yml에서 변경할 수 있습니다!)';

                    case 'fr':
                        return 'Sélectionnez une emote pour le PNJ (le minuteur des emotes peut être modifié dans Settings.yml !)';
                }
                break;
        }
    }
}