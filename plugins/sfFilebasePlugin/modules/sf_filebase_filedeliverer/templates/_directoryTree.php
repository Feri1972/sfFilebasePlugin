<?php
/**
 * This file is part of the sfFilebasePlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   de.optimusprime.sfFilebasePlugin.adminArea
 * @author    Johannes Heinen <johannes.heinen@gmail.com>
 * @license   MIT license
 * @copyright 2007-2009 Johannes Heinen <johannes.heinen@gmail.com>
 */
?>
<?php use_stylesheet('/sfFilebasePlugin/js/vendor/ext/resources/css/ext-all.css','first')?>
<?php use_stylesheet('/sfFilebasePlugin/js/vendor/ext/resources/css/xtheme-blue.css','first')?>
<?php use_javascript('/sfFilebasePlugin/js/vendor/ext/adapter/ext/ext-base.js');?>
<?php use_javascript('/sfFilebasePlugin/js/vendor/ext/ext-all.js');?>
<div class="sf-filebase-tree-container" id="<?php echo $id?>">
</div>
<script type="text/javascript" defer="defer">
//<![CDATA[
var <?php echo $id?> = function()
{
  // shorthand
  var Tree = Ext.tree;
  return {
    init : function()
    {
      // yui-ext tree
      var tree = new Tree.TreePanel
      ({
          el:'<?php echo $id?>',
          animate:true,
          autoScroll:true,
          loader: new Tree.TreeLoader({dataUrl:"<?php echo url_for('@sf_filebase_get_tree')?>"}),
          enableDD:true,
          containerScroll: true,
          border: false,
          ddAppendOnly : true
          //dropConfig: {appendOnly:true}
      });

      // add a tree sorter in folder mode
      new Tree.TreeSorter(tree, {folderSort:true});

      // set the root node
      var root = new Tree.AsyncTreeNode({
          text: '/',
          draggable:false, // disable root node dragging
          id: '<?php echo $root->getId()?>'
      });
      tree.setRootNode(root);

      var oldPosition     = null;
      var oldNextSibling  = null;
      tree.on('startdrag', function(tree, node, event)
      {
        oldPosition = node.parentNode.indexOf(node);
        oldNextSibling = node.nextSibling;
      });

      tree.on('movenode', function(tree, node, oldParent, newParent, position)
      {
        var params =
        {
          'node':     node.id,
          'leaf':     node.leaf ? '1' : '0',
          'parent':   newParent.id,
          'position': position.toString()
        };

        // we disable tree interaction until we've heard a response from the server
        // this prevents concurrent requests which could yield unusual results
        tree.disable();

        Ext.Ajax.request
        ({
          'url': "<?php echo url_for('@sf_filebase_move_file')?>",
          'params': params,
          success:function(response, request)
          {
            // if the first char of our response is not 1, then we fail the operation,
            // otherwise we re-enable the tree
            if (response.responseText.charAt(0) != 1)
            {
              request.failure();
            }
            else
            {
              //tree.getRootNode().reload();
              tree.enable();
            }
          },
          failure:function()
          {
            // we move the node back to where it was beforehand and
            // we suspendEvents() so that we don't get stuck in a possible infinite loop
            tree.suspendEvents();
            oldParent.appendChild(node);
            if (oldNextSibling){
              oldParent.insertBefore(node, oldNextSibling);
            }
            tree.resumeEvents();
            tree.enable();
            alert("An error occured while saving the changes on the file tree.");
          }
        });
      });

      // render the tree
      tree.render();
    }
  };
}();
Ext.EventManager.onDocumentReady(<?php echo $id?>.init, <?php echo $id?>, true);
//]]>
</script>