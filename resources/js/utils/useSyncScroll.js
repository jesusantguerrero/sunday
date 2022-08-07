export const useSyncScroll = (direction, theClass) => {
    const syncScroll = (scrollEvent) => {
        const propsNames = {
            left: 'scrollLeft',
            top: 'scrollTop'
        }
        const newPosition = scrollEvent.target[propsNames[direction]]
        document.querySelectorAll(`.${theClass}`).forEach((item) => {
            if (item.id !== scrollEvent.target.id)
            item.scrollTo({
                [direction]:  newPosition,
            })
        })
    }

    return { 
        syncScroll
    }
}