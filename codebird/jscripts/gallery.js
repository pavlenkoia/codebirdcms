hs.graphicsDir = 'jscripts/highslide/graphics/';
                hs.outlineType = 'rounded-white';
                hs.wrapperClassName = 'draggable-header';
                hs.lang = {
                    cssDirection: 'ltr',
                    loadingText: 'Загружается...',
                    loadingTitle: 'Нажмите для отмены',
                    focusTitle: 'Нажмите чтобы поместить на передний план',
                    fullExpandTitle: 'Развернуть до оригинального размера',
                    creditsText: '',
                    creditsTitle: '',
                    previousText: 'Предыдущее',
                    nextText: 'Следующее',
                    moveText: 'Переместить',
                    closeText: 'Закрыть',
                    closeTitle: 'Закрыть (esc)',
                    resizeTitle: 'Изменить размер',
                    playText: 'Слайдшоу',
                    playTitle: 'Начать слайдшоу (пробел)',
                    pauseText: 'Пауза',
                    pauseTitle: 'Приостановить слайдшоу (пробел)',
                    previousTitle: 'Предыдущее (стрелка влево)',
                    nextTitle: 'Следующее (стрелка вправо)',
                    moveTitle: 'Переместить',
                    fullExpandText: 'Оригинальный размер',
                    number: 'Изображение %1 из %2',
                    restoreTitle: 'Нажмите чтобы закрыть изображение, нажмите и перетащите для изменения местоположения. Для просмотра изображений используйте стрелки.'
                };
				
$(function(){
    $("a.hs").click(function(){
        return hs.expand(this);
	});
});