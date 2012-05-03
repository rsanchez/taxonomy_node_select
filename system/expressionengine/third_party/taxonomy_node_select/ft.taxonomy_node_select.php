<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Taxonomy_node_select_ft extends EE_Fieldtype
{
	public $info = array(
		'name' => 'Taxonomy Node Select',
		'version' => '1.0.0'
	);
	
	/**
	 * Display Field on Publish
	 *
	 * @access	public
	 * @param	$data
	 * @return	field html
	 *
	 */
	public function display_field($data, $cell = FALSE)
	{
		$query = $this->EE->db->select('id, label')
								->where('site_id', $this->EE->config->item('site_id'))
								->get('taxonomy_trees');

		if ($query->num_rows() === 0)
		{
			return 'No Taxonomy trees found.';
		}

		$this->EE->load->add_package_path(PATH_THIRD.'taxonomy/');

		$this->EE->load->library('ttree');

		$options = array();

		foreach ($query->result() as $row)
		{
			$tree_array = $this->EE->ttree->get_tree_array($row->id);

			if ($tree_array[0]['has_children'] !== 'yes')
			{
				continue;
			}

			$options[$row->label] = array();

			$this->add_children($options[$row->label], $tree_array[0]);
		}

		$field_name = $cell ? $this->cell_name : $this->field_name;

		return form_dropdown($field_name, $options, $data);
	}

	private function add_children(&$array, $node, $depth = 0)
	{
		if ( ! isset($node['has_children']) || $node['has_children'] !== 'yes')
		{
			return;
		}

		if ($depth !== 0)
		{
			$array[$node['node_id']] = str_repeat('- ', $depth).$node['label'];
		}

		foreach ($node['children'] as $_node)
		{
			$array[$_node['node_id']] = str_repeat('- ', $depth + 1).$_node['label'];

			$this->add_children($array, $_node, $depth + 1);
		}
	}
	
	/**
	 * Replace tag
	 *
	 * @access	public
	 * @param	field contents
	 * @return	replacement text
	 *
	 */
	public function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $data;
	}

	public function save($data)
	{
		return $data;
	}

	public function display_cell($data)
	{
		return $this->display_field($data, TRUE);
	}

	public function save_cell($data)
	{
		return $this->save($data);
	}

	public function display_var_field($data)
	{
		return $this->display_field($data);
	}

	public function save_var_field($data)
	{
		return $this->save($data);
	}

	public function display_var_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $this->replace_tag($data, $params, $tagdata);
	}
}

/* End of file ft.google_maps.php */
/* Location: ./system/expressionengine/third_party/google_maps/ft.google_maps.php */