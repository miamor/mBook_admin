<?php
$review->content = $content = isset($_POST['content']) ? $_POST['content'] : null;
$review->toFB = $toFB = isset($_POST['to_fb']) ? $_POST['to_fb'] : false;
$review->thumb = $thumb = isset($_FILES['thumb']) ? $_FILES['thumb'] : null;
$review->thumbPath = null;
$review->title = isset($_POST['book']) ? $_POST['book'] : null;
$review->iid = $iid = isset($_POST['bid']) ? $_POST['bid'] : 0;
$review->rate = $rate = isset($_POST['rate']) ? $_POST['rate'] : 0;
$review->uid = $uid = isset($_POST['uid']) ? $_POST['uid'] : $config->u;
$review->status = $status = isset($_POST['status']) ? $_POST['status'] : 0;
//print_r($thumb);
if ( (!$toFB || ($toFB && $thumb) ) && $content && $rate) {
	$theRv = $review->sReadOne();
	if (isset($theRv['id']) && $theRv['id']) echo '[type]error[/type][content]Duplicated content found![/content]';
	else {
		$review->content = $content;
		$review->iid = $iid = isset($_POST['bid']) ? $_POST['bid'] : 0;
		$review->rate = $rate = isset($_POST['rate']) ? $_POST['rate'] : 0;

		if ($content && ($iid || $review->title)) {
//			echo $content.'~'.$iid.'~'.$review->title;
			if ($thumb) $review->thumbPath = $review->upload($thumb);
			$create = $review->create();
			if ($create) {
				if ($toFB) {
					$text = $review->content."<br/>".$review->link;
					$breaks = array("<br />","<br>","<br/>");
					$text = str_ireplace($breaks, "\r\n", $text);
					$data = [
						'message' => str_replace('&nbsp;', ' ', rtrim(strip_tags($text))),
						'source' => $config->FB->fileToUpload($review->thumbPath),
					];

					try {
						// Returns a `Facebook\FacebookResponse` object
						$response = $config->FB->post('/me/photos', $data, $config->me['oauth_token']);
					} catch(Facebook\Exceptions\FacebookResponseException $e) {
						echo 'Graph returned an error: ' . $e->getMessage();
						exit;
					} catch(Facebook\Exceptions\FacebookSDKException $e) {
						echo 'Facebook SDK returned an error: ' . $e->getMessage();
						exit;
					}
					$graphNode = $response->getGraphNode();

					if (isset($graphNode['id']) && $graphNode['id']) {
						$review->fb_post_id = $graphNode['id'];
						$review->updateFB_postID();
						echo '[type]success[/type][dataID]'.$review->link.'[/dataID][content]Review created successfully. Redirecting...[/content]';
					} else echo '[type]warning[/type][dataID]'.$review->link.'[/dataID][content]Có lỗi trong quá trình upload lên Facebook. Vui lòng liên hệ administrators để biết thêm chi tiết. Đang chuyển trang tới <a href="'.$review->link.'">'.$review->link.'</a>...[/content]';
				}
				else echo '[type]success[/type][dataID]'.$review->link.'[/dataID][content]Review created successfully. Redirecting...[/content]';
			} else echo '[type]error[/type][content]Oops! Something went wrong with our system. Please contact the administrators for furthur help.[/content]';
		} else echo '[type]error[/type][content]Missing parameters![/content]';
	}
} else echo '[type]error[/type][content]Missing parameters[/content]';
