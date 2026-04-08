<?php
declare(strict_types=1);

namespace Controller;

use Core\ViewRenderer;
use Core\Request;
use Core\Session;
use Core\Response;
use Service\DashboardService;

class DashboardController
{

    public function __construct(
        private Request $request,
        private Response $response,
        private Session $session
    ) {
    }

    /**
     * ダッシュボード画面を表示する
     */
    public function showDashboard(ViewRenderer $viewRenderer, DashboardService $dashboardService): never
    {
        $userID = $this->session->getStr('user_id');

        if (!$this->session->has('vtuber_channels') && !$this->session->has('registered_channelIds')) {

            $this->session->setArray('vtuber_channels', $dashboardService->getAllVtuberData());

            // リマインダー登録しているチャンネルIDをDBから取得してセッションに保存する
            $this->session->setArray('registered_channelIds', $dashboardService->getRegisteredChannelIds($userID));
        }

        $registeredChannelIds["registeredChannels"] = $this->session->getArray('registered_channelIds');
        $vtuberChannelList['vtuberAllChannels'] = $this->session->getArray('vtuber_channels');

        $viewRenderer->render('dashboard', array_merge($vtuberChannelList, $registeredChannelIds));
    }

    /**
     * ユーザーIDと選択したchannelIDを紐付ける<br>
     * 選択したchannelIDがユーザーと紐付くchannelIDと重複しているか比較する<br>
     * 重複していない場合、ユーザーIDとchannelIDを紐付ける
     */
    public function assignChannelIdToUser(DashboardService $dashboardService)
    {
        $selectChannelIds = $this->request->fetchInputValue('selected_members');
        $userId = $this->session->getStr('user_id');

        // 未登録のchannelIDを取得する
        $unregisteredChannelIds = $dashboardService->fetchUnregisteredChannelIds($selectChannelIds, $userId);

        if (!empty($unregisteredChannelIds)) {

            // 未登録のchannelIDをユーザーIDと紐付ける
            $dashboardService->assignUserIdToUnregisteredChannelIds($unregisteredChannelIds, $userId);

            $this->response->redirect('/dashboard');
        }

        $this->response->redirect('/dashboard');
    }
}