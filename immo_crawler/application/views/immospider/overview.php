<div class="container-fluid" id="urlGenForm">
    <?php echo form_open('immospider/create_url'); ?>
        <div class="form-group">
            <label for="immoprice">Maximalpreis</label>
            <input type="text" class="form-control" id="immoprice" placeholder="Preis eingeben" name="immoprice">
        </div>
        <div class="form-group">
            <label for="immocity">Stadt</label>
            <select class="form-control" id="immocity" name="immocity">
                <option value="Berlin">Berlin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="immotype">Typ</label>
            <select class="form-control" id="immotype" name="immotype">
                <option value="Wohnung-Kauf">Eigentumswohnung</option>
                <option value="Haus-Kauf">Haus</option>
            </select>
        </div>
        <button type="submit" class="btn btn-default">Create</button>
    </form>
    <?php if(isset($additional['createdUrl'])){ ?>
    <div id="generatedUrl">
        <blockquote>
            <p><a href="<?php echo $additional['createdUrl']; ?>" id="genUrlLink" target="_blank"><?php echo $additional['createdUrl']; ?></a></p>
            <footer>Generierter Link</footer>
        </blockquote>
    </div>
    <?php echo form_open('immospider/save_url'); ?>
        <input type="hidden" name="createdUrl" value="<?php echo $additional['createdUrl']; ?>" />
        <button type="submit" class="btn btn-default">Speichern</button>
    </form>
    <?php } ?>
</div>

<table class="table table-striped">
<?php foreach ($urls as $news_item): ?>
<tr>
    <td>
        <a href="<?php echo $news_item['URL']; ?>" target="_blank">
        <?php echo $news_item['URL']; ?>
        </a>
    </td>
    <td>
        (<?php echo $news_item['ITEMCOUNT']; ?> Objekte gesamt)
    </td>
    <td>
        (<?php echo $news_item['NEWOBJECTCOUNT']; ?> neue Objekte)
    </td>
    <td>
        <?php echo form_open('immospider/delete_url'); ?>
            <input type="hidden" name="uniqueURL" value="<?php echo $news_item['URL']; ?>"/>
            <button type="submit" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>
