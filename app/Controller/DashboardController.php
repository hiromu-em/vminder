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
        if (!$this->session->has('vtuber_channelList')) {

            // DBから全てのvtuberデータを取得してSessionに保存する
            $this->session->setArray('vtuber_channelList', $dashboardService->getAllVtuberData());
        }

        $vtuberChannelList['vtuberChannels'] = $this->session->getArray('vtuber_channelList');
        $viewRenderer->render('dashboard', $vtuberChannelList);
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