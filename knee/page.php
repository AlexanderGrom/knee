<?php
/*
 * Knee framework
 * Назначение: Пагинация
 */

namespace Knee;

class Page
{
	/**
	 * Пагинация
	 */
	public static function make($total_item, $view_item, $view_page, $current_page, $limit_page = null)
	{
		$exists_page = (is_numeric($current_page) AND $current_page >= 0) ? 1 : 0;

		$current_page = (int) $current_page;
		$current_page = ($current_page <= 0) ? 1 : $current_page;

		$start = $current_page * $view_item - $view_item;

		if ($limit_page != null) {
			$limit_page = $view_item * $limit_page;
			$total_item = ($total_item > $limit_page) ? $limit_page : $total_item;
		}

		$total_page = ceil($total_item/$view_item);
		$total_page = ($total_page == 0) ? 1 : $total_page;

		$page_list = array();

		if ($view_page != 0) {
			$page_parts = floor($view_page/2);

			$offset = ($current_page > ($page_parts+1)) ? $current_page - $page_parts : 1;
			$offset = ($current_page > ($total_page-$page_parts)) ? $total_page - $view_page + 1 : $offset;
			$offset = ($offset <= 0) ? 1 : $offset;

			$iter = ($current_page > $page_parts) ? $current_page + $page_parts : $view_page;
			$iter = ($iter > $total_page) ? $total_page : $iter;

			for ($i=$offset; $i<=$iter; $i++) {
				$page_list[] = $i;
			}
		}

		$prev_page = $current_page-1;
		$next_page = $current_page+1;

		$result = array();
		$result['list'] = $page_list;
		$result['start'] = $start;
		$result['step'] = $view_item;
		$result['total'] = $total_page;
		$result['exists'] = $exists_page;
		$result['prev'] = $prev_page;
		$result['current'] = $current_page;
		$result['next'] = $next_page;

		return new \Knee\Page\Make($result);
	}
}

?>